<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use App\Core\Support\UserType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Ecommerce\Domain\Models\Shop;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\ShopStoreRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\ShopUpdateRequest;
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
        $userType = ($user?->user_type instanceof UserType) ? $user->user_type : null;
        $userTypeStr = $userType ? $userType->value : strtoupper((string) $user?->user_type);

        // Admin/System được xem all shops.
        $isPrivileged = $userTypeStr === 'SYSTEM' || $userTypeStr === 'ADMIN';

        $q = Shop::query()
            ->whereNull('deleted_at')
            ->orderBy('id');

        if (!$isPrivileged && $user) {
            $q->whereHas('users', fn ($qq) => $qq->where('users.id', $user->id));
        }

        $items = $q->get();

        return ApiResponse::success(
            data: ['items' => ShopResource::collection($items)],
            code: 'ECM_SHOP_LIST_SUCCESS',
            message: 'Lấy danh sách shop thành công',
        );
    }

    public function show(int $id)
    {
        /** @var Shop $shop */
        $shop = Shop::query()->whereNull('deleted_at')->findOrFail($id);

        return ApiResponse::success(
            data: ['shop' => new ShopResource($shop)],
            code: 'ECM_SHOP_SHOW_SUCCESS',
            message: 'Lấy shop thành công',
        );
    }

    public function store(ShopStoreRequest $request)
    {
        $input = $request->validated();
        $input['code'] = Str::upper(trim((string) $input['code']));
        $input['name'] = trim((string) $input['name']);
        $input['domain'] = isset($input['domain']) ? trim((string) $input['domain']) : null;
        $input['timezone'] = (string) ($input['timezone'] ?? 'Asia/Ho_Chi_Minh');
        $input['currency'] = Str::upper((string) ($input['currency'] ?? 'VND'));
        $input['is_active'] = (bool) ($input['is_active'] ?? true);
        $input['created_by'] = optional($request->user())->id;

        $shop = DB::transaction(fn () => Shop::query()->create($input));

        return ApiResponse::success(
            data: ['shop' => new ShopResource($shop)],
            code: 'ECM_SHOP_CREATE_SUCCESS',
            message: 'Tạo shop thành công',
            status: 201,
        );
    }

    public function update(int $id, ShopUpdateRequest $request)
    {
        /** @var Shop $shop */
        $shop = Shop::query()->whereNull('deleted_at')->findOrFail($id);

        $input = $request->validated();
        if (array_key_exists('code', $input)) {
            $input['code'] = Str::upper(trim((string) $input['code']));
        }
        if (array_key_exists('name', $input)) {
            $input['name'] = trim((string) $input['name']);
        }
        if (array_key_exists('domain', $input)) {
            $d = $input['domain'];
            $d = is_string($d) ? trim($d) : $d;
            $input['domain'] = $d === '' ? null : $d;
        }
        if (array_key_exists('currency', $input)) {
            $input['currency'] = Str::upper((string) $input['currency']);
        }

        $shop = DB::transaction(function () use ($shop, $input) {
            $shop->fill($input);
            $shop->save();
            return $shop->refresh();
        });

        return ApiResponse::success(
            data: ['shop' => new ShopResource($shop)],
            code: 'ECM_SHOP_UPDATE_SUCCESS',
            message: 'Cập nhật shop thành công',
        );
    }

    public function destroy(int $id)
    {
        /** @var Shop $shop */
        $shop = Shop::query()->whereNull('deleted_at')->findOrFail($id);

        DB::transaction(fn () => $shop->delete());

        return ApiResponse::success(
            data: [],
            code: 'ECM_SHOP_DELETE_SUCCESS',
            message: 'Xoá shop thành công',
        );
    }
}
