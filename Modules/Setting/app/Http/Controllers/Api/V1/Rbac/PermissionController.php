<?php

namespace Modules\Setting\Http\Controllers\Api\V1\Rbac;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\RbacServiceInterface;
use Modules\Setting\Http\Requests\Api\V1\Rbac\PermissionStoreRequest;
use Modules\Setting\Http\Resources\Api\V1\Rbac\PermissionResource;

/**
 * @group RBAC
 */
final class PermissionController extends Controller
{
    public function __construct(
        private readonly RbacServiceInterface $rbac,
    ) {}

    public function index()
    {
        $items = $this->rbac->listPermissions();

        return ApiResponse::success(
            data: ['items' => PermissionResource::collection($items)],
            code: 'RBAC_PERMISSION_LIST_SUCCESS',
            message: 'Lấy danh sách permission thành công',
        );
    }

    public function store(PermissionStoreRequest $request)
    {
        $permission = $this->rbac->createPermission((string) $request->validated('name'));

        return ApiResponse::success(
            data: ['permission' => new PermissionResource($permission)],
            code: 'RBAC_PERMISSION_CREATE_SUCCESS',
            message: 'Tạo permission thành công',
            status: 201,
        );
    }
}

