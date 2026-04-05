<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Domain\Models\Customer;
use Modules\Ecommerce\Domain\Models\Order;
use Modules\Ecommerce\Domain\Models\Product;
use Modules\Ecommerce\Support\ShopResolver;

/**
 * @group Ecommerce
 * @subgroup Admin - Dashboard
 */
final class DashboardController extends Controller
{
    public function overview()
    {
        $shopId = ShopResolver::id();

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
}

