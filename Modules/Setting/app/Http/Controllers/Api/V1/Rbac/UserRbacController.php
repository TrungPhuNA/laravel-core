<?php

namespace Modules\Setting\Http\Controllers\Api\V1\Rbac;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\RbacServiceInterface;
use Modules\Setting\Http\Requests\Api\V1\Rbac\UserSyncPermissionsRequest;
use Modules\Setting\Http\Requests\Api\V1\Rbac\UserSyncRolesRequest;
use Modules\Setting\Http\Resources\Api\V1\Rbac\UserRbacResource;

/**
 * @group RBAC
 */
final class UserRbacController extends Controller
{
    public function __construct(
        private readonly RbacServiceInterface $rbac,
    ) {}

    public function show(int $id)
    {
        $user = $this->rbac->getUserById($id);

        return ApiResponse::success(
            data: ['user' => new UserRbacResource($user)],
            code: 'RBAC_USER_SHOW_SUCCESS',
            message: 'Lấy phân quyền user thành công',
        );
    }

    public function syncRoles(int $id, UserSyncRolesRequest $request)
    {
        $user = $this->rbac->syncUserRoles($id, (array) $request->validated('roles'));

        return ApiResponse::success(
            data: ['user' => new UserRbacResource($user)],
            code: 'RBAC_USER_SYNC_ROLES_SUCCESS',
            message: 'Cập nhật role cho user thành công',
        );
    }

    public function syncPermissions(int $id, UserSyncPermissionsRequest $request)
    {
        $user = $this->rbac->syncUserPermissions($id, (array) $request->validated('permissions'));

        return ApiResponse::success(
            data: ['user' => new UserRbacResource($user)],
            code: 'RBAC_USER_SYNC_PERMISSIONS_SUCCESS',
            message: 'Cập nhật permission trực tiếp cho user thành công',
        );
    }
}

