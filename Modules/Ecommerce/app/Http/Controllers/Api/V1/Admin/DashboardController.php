<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Core\Support\UserType;
use Modules\Ecommerce\Domain\Models\Customer;
use Modules\Ecommerce\Domain\Models\Order;
use Modules\Ecommerce\Domain\Models\Product;
use Modules\Ecommerce\Domain\Models\Shop;
use Modules\Ecommerce\Support\ShopContext;
use Modules\Ecommerce\Support\ShopResolver;

/**
 * @group Ecommerce
 * @subgroup Admin - Dashboard
 */
final class DashboardController extends Controller
{
    /**
     * @return array{0: string, 1: int, 2: \Illuminate\Support\Carbon, 3: \Illuminate\Support\Carbon, 4: \Illuminate\Support\Carbon, 5: \Illuminate\Support\Carbon}
     */
    private function parseRange(): array
    {
        $range = strtoupper(trim((string) request()->query('range', '30d')));
        $range = $range !== '' ? $range : '30D';

        $unit = 'day';
        $points = 30;
        if ($range === '7D') {
            $unit = 'day';
            $points = 7;
        } elseif ($range === '90D') {
            $unit = 'day';
            $points = 90;
        } elseif ($range === '12M') {
            $unit = 'month';
            $points = 12;
        } else {
            $unit = 'day';
            $points = 30;
        }

        $now = now();
        $from = $unit === 'month'
            ? $now->copy()->startOfMonth()->subMonths($points - 1)
            : $now->copy()->startOfDay()->subDays($points - 1);
        $to = $unit === 'month'
            ? $now->copy()->endOfMonth()
            : $now->copy()->endOfDay();

        $prevFrom = $unit === 'month'
            ? $from->copy()->subMonths($points)
            : $from->copy()->subDays($points);
        $prevTo = $unit === 'month'
            ? $to->copy()->subMonths($points)
            : $to->copy()->subDays($points);

        return [$unit, $points, $from, $to, $prevFrom, $prevTo];
    }

    public function overview()
    {
        $shopId = ShopResolver::id();
        $shop = app(ShopContext::class)->shop;

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $ordersToday = Order::query()
            ->where('shop_id', $shopId)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        $revenueToday = (float) Order::query()
            ->where('shop_id', $shopId)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->whereNotNull('paid_at')
            ->sum('total');

        $ordersTotal = Order::query()->where('shop_id', $shopId)->count();
        $customersTotal = Customer::query()->where('shop_id', $shopId)->count();
        $productsTotal = Product::query()->where('shop_id', $shopId)->count();

        $lowStock = Product::query()
            ->where('shop_id', $shopId)
            ->where('track_inventory', true)
            ->where('stock_qty', '<=', 5)
            ->count();

        $byStatus = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->where('shop_id', $shopId)
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => ['status' => (string) $row->status, 'total' => (int) $row->total])
            ->values();

        return ApiResponse::success(
            data: [
                'shop_id' => $shopId,
                'shop' => $shop ? ['id' => $shop->id, 'code' => $shop->code, 'name' => $shop->name] : null,
                'cards' => [
                    'orders_today' => $ordersToday,
                    'revenue_today' => $revenueToday,
                    'orders_total' => $ordersTotal,
                    'customers_total' => $customersTotal,
                    'products_total' => $productsTotal,
                    'low_stock_products' => $lowStock,
                ],
                'orders_by_status' => $byStatus,
            ],
            code: 'ECM_DASHBOARD_OVERVIEW_SUCCESS',
            message: 'Lấy dashboard thành công',
        );
    }

    /**
     * Revenue series để vẽ biểu đồ.
     *
     * Query:
     * - range: 7d|30d|90d|12m (default: 30d)
     */
    public function revenue()
    {
        $shopId = ShopResolver::id();
        $shop = app(ShopContext::class)->shop;

        [$unit, $points, $from, $to, $prevFrom, $prevTo] = $this->parseRange();

        $driver = DB::connection()->getDriverName();

        if ($unit === 'month') {
            // Group by YYYY-MM.
            // MySQL: DATE_FORMAT(paid_at, '%Y-%m')
            // SQLite: strftime('%Y-%m', paid_at)
            $bucketExpr = match ($driver) {
                'sqlite' => "strftime('%Y-%m', paid_at)",
                default => "DATE_FORMAT(paid_at, '%Y-%m')",
            };

            $rows = Order::query()
                ->selectRaw($bucketExpr.' as bucket')
                ->selectRaw('COALESCE(SUM(total),0) as revenue')
                ->selectRaw('COUNT(*) as orders')
                ->where('shop_id', $shopId)
                ->whereNotNull('paid_at')
                ->whereBetween('paid_at', [$from, $to])
                ->groupBy('bucket')
                ->orderBy('bucket')
                ->get();

            $map = [];
            foreach ($rows as $r) {
                $k = (string) $r->bucket;
                $map[$k] = [
                    'x' => $k,
                    'revenue' => (float) $r->revenue,
                    'orders' => (int) $r->orders,
                ];
            }

            $series = [];
            $cursor = $from->copy();
            for ($i = 0; $i < $points; $i++) {
                $k = $cursor->format('Y-m');
                $series[] = $map[$k] ?? ['x' => $k, 'revenue' => 0.0, 'orders' => 0];
                $cursor->addMonth();
            }
        } else {
            // Group by YYYY-MM-DD.
            // MySQL/SQLite: DATE(paid_at)
            $bucketExpr = "DATE(paid_at)";

            $rows = Order::query()
                ->selectRaw($bucketExpr.' as bucket')
                ->selectRaw('COALESCE(SUM(total),0) as revenue')
                ->selectRaw('COUNT(*) as orders')
                ->where('shop_id', $shopId)
                ->whereNotNull('paid_at')
                ->whereBetween('paid_at', [$from, $to])
                ->groupBy('bucket')
                ->orderBy('bucket')
                ->get();

            $map = [];
            foreach ($rows as $r) {
                $k = (string) $r->bucket;
                $map[$k] = [
                    'x' => $k,
                    'revenue' => (float) $r->revenue,
                    'orders' => (int) $r->orders,
                ];
            }

            $series = [];
            $cursor = $from->copy();
            for ($i = 0; $i < $points; $i++) {
                $k = $cursor->format('Y-m-d');
                $series[] = $map[$k] ?? ['x' => $k, 'revenue' => 0.0, 'orders' => 0];
                $cursor->addDay();
            }
        }

        $totalRevenue = array_sum(array_map(static fn ($p) => (float) ($p['revenue'] ?? 0), $series));
        $totalOrders = array_sum(array_map(static fn ($p) => (int) ($p['orders'] ?? 0), $series));

        $prevTotalRevenue = (float) Order::query()
            ->where('shop_id', $shopId)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$prevFrom, $prevTo])
            ->sum('total');

        $revenueGrowthPercent = null;
        if ($prevTotalRevenue > 0) {
            $revenueGrowthPercent = (($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100.0;
        } elseif ($totalRevenue > 0) {
            $revenueGrowthPercent = 100.0;
        }

        return ApiResponse::success(
            data: [
                'shop_id' => $shopId,
                'shop' => $shop ? ['id' => $shop->id, 'code' => $shop->code, 'name' => $shop->name] : null,
                'range' => [
                    'key' => strtolower((string) request()->query('range', '30d')),
                    'unit' => $unit,
                    'points' => $points,
                    'from' => $from->toISOString(),
                    'to' => $to->toISOString(),
                ],
                'totals' => [
                    'revenue' => $totalRevenue,
                    'orders' => $totalOrders,
                    'prev_revenue' => $prevTotalRevenue,
                    'revenue_growth_percent' => $revenueGrowthPercent,
                ],
                'series' => $series,
            ],
            code: 'ECM_DASHBOARD_REVENUE_SUCCESS',
            message: 'Lấy dữ liệu doanh thu thành công',
        );
    }

    /**
     * Thống kê theo shop (ADMIN/SYSTEM: all shops; others: assigned shops).
     *
     * Query:
     * - range: 7d|30d|90d|12m (default: 30d)
     */
    public function shopsSummary()
    {
        [$unit, $points, $from, $to] = $this->parseRange();

        $user = request()->user();
        $userType = ($user?->user_type instanceof UserType) ? $user->user_type : null;
        $userTypeStr = $userType ? $userType->value : strtoupper((string) $user?->user_type);
        $isPrivileged = $userTypeStr === 'SYSTEM' || $userTypeStr === 'ADMIN';

        $shopsQuery = Shop::query()
            ->whereNull('deleted_at')
            ->where('is_active', true);

        if (!$isPrivileged && $user) {
            $shopsQuery->whereHas('users', fn ($q) => $q->where('users.id', $user->id));
        }

        $shops = $shopsQuery->orderBy('id')->get(['id', 'code', 'name']);
        $shopIds = $shops->pluck('id')->map(fn ($v) => (int) $v)->all();

        $revRows = [];
        if ($shopIds !== []) {
            $revRows = Order::query()
                ->selectRaw('shop_id, COALESCE(SUM(total),0) as revenue, COUNT(*) as orders_paid')
                ->whereIn('shop_id', $shopIds)
                ->whereNotNull('paid_at')
                ->whereBetween('paid_at', [$from, $to])
                ->groupBy('shop_id')
                ->get()
                ->keyBy('shop_id')
                ->all();
        }

        $items = $shops->map(function (Shop $s) use ($revRows) {
            $row = $revRows[$s->id] ?? null;
            return [
                'shop' => ['id' => (int) $s->id, 'code' => (string) $s->code, 'name' => (string) $s->name],
                'revenue' => $row ? (float) $row->revenue : 0.0,
                'orders_paid' => $row ? (int) $row->orders_paid : 0,
            ];
        })->sortByDesc('revenue')->values();

        return ApiResponse::success(
            data: [
                'range' => [
                    'unit' => $unit,
                    'points' => $points,
                    'from' => $from->toISOString(),
                    'to' => $to->toISOString(),
                ],
                'items' => $items,
            ],
            code: 'ECM_DASHBOARD_SHOPS_SUMMARY_SUCCESS',
            message: 'Lấy thống kê theo shop thành công',
        );
    }
}
