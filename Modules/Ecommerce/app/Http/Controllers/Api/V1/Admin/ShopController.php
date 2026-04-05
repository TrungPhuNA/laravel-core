<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use App\Core\Support\UserType;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Domain\Models\Shop;
use Modules\Ecommerce\Http\Resources\Api\V1\ShopResource;

/**
 * @group Ecommerce
 * @subgroup Admin - Shops
 */
final class ShopController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $isSystem = ($user?->user_type instanceof UserType)
            ? $user->user_type === UserType::SYSTEM
            : strtoupper((string) $user?->user_type) === 'SYSTEM';

        $q = Shop::query()
            ->whereNull('deleted_at')
            ->orderBy('id');

        if (!$isSystem && $user) {
            $q->whereHas('users', fn ($qq) => $qq->where('users.id', $user->id));
        }

        $items = $q->get();

        return ApiResponse::success(
            data: ['items' => ShopResource::collection($items)],
            code: 'ECM_SHOP_LIST_SUCCESS',
            message: 'Lấy danh sách shop thành công',
        );
    }
}

