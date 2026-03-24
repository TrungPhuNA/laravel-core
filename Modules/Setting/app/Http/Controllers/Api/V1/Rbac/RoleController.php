<?php

namespace Modules\Setting\Http\Controllers\Api\V1\Rbac;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\RbacServiceInterface;
use Modules\Setting\Http\Requests\Api\V1\Rbac\RoleStoreRequest;
use Modules\Setting\Http\Requests\Api\V1\Rbac\RoleUpdateRequest;
use Modules\Setting\Http\Resources\Api\V1\Rbac\RoleResource;

/**
 * @group RBAC
 */
final class RoleController extends Controller
{
    public function __construct(
        private readonly RbacServiceInterface $rbac,
    ) {}

    public function index()
    {
        $items = $this->rbac->listRoles();

        return ApiResponse::success(
            data: ['items' => RoleResource::collection($items)],
            code: 'RBAC_ROLE_LIST_SUCCESS',
            message: 'Lấy danh sách role thành công',
        );
    }

    public function show(int $id)
    {
        $role = $this->rbac->getRoleById($id);

        return ApiResponse::success(
            data: ['role' => new RoleResource($role)],
            code: 'RBAC_ROLE_SHOW_SUCCESS',
            message: 'Lấy role thành công',
        );
    }

    public function store(RoleStoreRequest $request)
    {
        $role = $this->rbac->createRole(
            name: (string) $request->validated('name'),
            permissionNames: (array) ($request->validated('permissions') ?? []),
        );

        return ApiResponse::success(
            data: ['role' => new RoleResource($role)],
            code: 'RBAC_ROLE_CREATE_SUCCESS',
            message: 'Tạo role thành công',
            status: 201,
        );
    }

    public function update(int $id, RoleUpdateRequest $request)
    {
        $role = $this->rbac->updateRole($id, $request->validated());

        return ApiResponse::success(
            data: ['role' => new RoleResource($role)],
            code: 'RBAC_ROLE_UPDATE_SUCCESS',
            message: 'Cập nhật role thành công',
        );
    }

    public function destroy(int $id)
    {
        $this->rbac->deleteRole($id);

        return ApiResponse::success(
            data: [],
            code: 'RBAC_ROLE_DELETE_SUCCESS',
            message: 'Xoá role thành công',
        );
    }
}

