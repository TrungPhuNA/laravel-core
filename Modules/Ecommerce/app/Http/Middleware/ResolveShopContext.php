<?php

namespace Modules\Ecommerce\Http\Middleware;

use App\Core\Http\Responses\ApiResponse;
use App\Core\Support\UserType;
use Closure;
use Illuminate\Http\Request;
use Modules\Ecommerce\Domain\Models\Shop;
use Modules\Ecommerce\Support\ShopContext;

final class ResolveShopContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::fail([], 'UNAUTHORIZED', 'Unauthenticated', 401);
        }

        $wanted = $request->header('X-Shop-Id');
        $wanted = is_string($wanted) ? (int) trim($wanted) : null;

        $userType = ($user->user_type instanceof UserType)
            ? $user->user_type
            : null;

        $userTypeStr = $userType ? $userType->value : strtoupper((string) $user->user_type);

        // Quy ước: ADMIN được xem/tác vụ trên tất cả shop (đồ án/demo dễ test).
        // SYSTEM cũng tương tự.
        $isPrivileged = $userTypeStr === 'SYSTEM' || $userTypeStr === 'ADMIN';

        $shopQuery = Shop::query()
            ->whereNull('deleted_at')
            ->where('is_active', true);

        if ($wanted && $wanted > 0) {
            if (!$isPrivileged) {
                $shopQuery->whereHas('users', fn ($q) => $q->where('users.id', $user->id));
            }

            /** @var Shop|null $shop */
            $shop = $shopQuery->where('id', $wanted)->first();
            if (!$shop) {
                return ApiResponse::fail(
                    data: ['shop_id' => ['Invalid shop or no access']],
                    code: 'SHOP_NOT_FOUND',
                    message: 'Shop không tồn tại hoặc bạn không có quyền truy cập',
                    status: 400,
                );
            }

            app()->instance(ShopContext::class, new ShopContext(shopId: (int) $shop->id, shop: $shop));
            $request->attributes->set('ecm_shop_id', (int) $shop->id);

            return $next($request);
        }

        // Auto-pick first accessible shop.
        if (!$isPrivileged) {
            $shopQuery->whereHas('users', fn ($q) => $q->where('users.id', $user->id));
        }

        /** @var Shop|null $shop */
        $shop = $shopQuery->orderBy('id')->first();
        if (!$shop) {
            return ApiResponse::fail(
                data: [],
                code: 'SHOP_NOT_FOUND',
                message: 'Chưa có shop nào được gán cho tài khoản này',
                status: 400,
            );
        }

        app()->instance(ShopContext::class, new ShopContext(shopId: (int) $shop->id, shop: $shop));
        $request->attributes->set('ecm_shop_id', (int) $shop->id);

        return $next($request);
    }
}
