<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Domain\Models\Shop;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\ShopSyncUsersRequest;

/**
 * @group Ecommerce
 * @subgroup Admin - Shop Users
 */
final class ShopUserController extends Controller
{
    public function index(int $id)
    {
        /** @var Shop $shop */
        $shop = Shop::query()->whereNull('deleted_at')->findOrFail($id);

        $items = $shop->users()
            ->select(['users.id', 'users.name', 'users.email', 'users.user_type'])
            ->orderBy('users.id')
            ->get()
            ->map(function (User $u) {
                return [
                    'id' => (int) $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'user_type' => $u->user_type?->value ?? (string) $u->user_type,
                    'shop_role' => (string) ($u->pivot?->role ?? ''),
                ];
            })
            ->values();

        return ApiResponse::success(
            data: [
                'shop' => ['id' => $shop->id, 'code' => $shop->code, 'name' => $shop->name],
                'items' => $items,
            ],
            code: 'ECM_SHOP_USERS_LIST_SUCCESS',
            message: 'Lấy danh sách user theo shop thành công',
        );
    }

    public function sync(int $id, ShopSyncUsersRequest $request)
    {
        /** @var Shop $shop */
        $shop = Shop::query()->whereNull('deleted_at')->findOrFail($id);

        $members = (array) $request->validated('members');
        $sync = [];

        foreach ($members as $m) {
            if (!is_array($m)) {
                continue;
            }
            $userId = (int) ($m['user_id'] ?? 0);
            if ($userId <= 0) {
                continue;
            }
            $role = strtoupper(trim((string) ($m['role'] ?? 'STAFF')));
            if ($role === '') {
                $role = 'STAFF';
            }
            $sync[$userId] = ['role' => $role];
        }

        // Validate user existence quickly
        $ids = array_keys($sync);
        if ($ids !== []) {
            $found = User::query()->whereIn('id', $ids)->pluck('id')->all();
            $missing = array_values(array_diff($ids, array_map('intval', $found)));
            if ($missing !== []) {
                return ApiResponse::fail(
                    data: ['members' => ['Unknown user_id: '.implode(',', $missing)]],
                    code: 'VALIDATION_ERROR',
                    message: __('messages.validation_error'),
                    status: 400,
                );
            }
        }

        DB::transaction(function () use ($shop, $sync) {
            $shop->users()->sync($sync);
        });

        return ApiResponse::success(
            data: [],
            code: 'ECM_SHOP_USERS_SYNC_SUCCESS',
            message: 'Cập nhật users theo shop thành công',
        );
    }

    public function detach(int $id, int $userId)
    {
        /** @var Shop $shop */
        $shop = Shop::query()->whereNull('deleted_at')->findOrFail($id);

        DB::transaction(function () use ($shop, $userId) {
            $shop->users()->detach($userId);
        });

        return ApiResponse::success(
            data: [],
            code: 'ECM_SHOP_USERS_DETACH_SUCCESS',
            message: 'Gỡ user khỏi shop thành công',
        );
    }
}

