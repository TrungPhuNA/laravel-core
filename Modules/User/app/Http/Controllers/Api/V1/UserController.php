<?php

namespace Modules\User\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\User\Application\Contracts\UserServiceInterface;
use Modules\User\Http\Requests\Api\V1\UserIndexRequest;
use Modules\User\Http\Requests\Api\V1\UserResetPasswordRequest;
use Modules\User\Http\Requests\Api\V1\UserStoreRequest;
use Modules\User\Http\Requests\Api\V1\UserUpdateRequest;
use Modules\User\Http\Requests\Api\V1\UserUpdateUserTypeRequest;
use Modules\User\Http\Resources\Api\V1\UserResource;

/**
 * @group Tài khoản
 */
final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
    ) {}

    /**
     * Danh sách tài khoản
     *
     * Hỗ trợ query:
     * - filter[name], filter[email], filter[user_type], filter[phone]
     * - sort=id,name,email,user_type,created_at,updated_at (có thể thêm dấu "-" để desc)
     * - page, per_page
     *
     * @subgroup Quản trị
     */
    public function index(UserIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $paginator = $this->users->paginate($params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: UserResource::collection($paginator->items()),
            code: 'USER_LIST_SUCCESS',
            message: 'Lấy danh sách tài khoản thành công',
        );
    }

    /**
     * Chi tiết tài khoản
     *
     * @subgroup Quản trị
     */
    public function show(int $id)
    {
        $user = $this->users->getById($id);

        return ApiResponse::success(
            data: ['user' => new UserResource($user)],
            code: 'USER_SHOW_SUCCESS',
            message: 'Lấy thông tin tài khoản thành công',
        );
    }

    /**
     * Tạo tài khoản
     *
     * @subgroup Quản trị
     */
    public function store(UserStoreRequest $request)
    {
        $user = $this->users->create($request->validated());

        return ApiResponse::success(
            data: ['user' => new UserResource($user)],
            code: 'USER_CREATE_SUCCESS',
            message: 'Tạo tài khoản thành công',
            status: 201,
        );
    }

    /**
     * Cập nhật tài khoản
     *
     * @subgroup Quản trị
     */
    public function update(int $id, UserUpdateRequest $request)
    {
        $user = $this->users->update($id, $request->validated());

        return ApiResponse::success(
            data: ['user' => new UserResource($user)],
            code: 'USER_UPDATE_SUCCESS',
            message: 'Cập nhật tài khoản thành công',
        );
    }

    /**
     * Đổi user_type
     *
     * @subgroup Quản trị
     */
    public function updateUserType(int $id, UserUpdateUserTypeRequest $request)
    {
        $userType = (string) $request->validated('user_type');
        $user = $this->users->updateUserType($id, $userType);

        return ApiResponse::success(
            data: ['user' => new UserResource($user)],
            code: 'USER_UPDATE_TYPE_SUCCESS',
            message: 'Cập nhật loại tài khoản thành công',
        );
    }

    /**
     * Reset mật khẩu
     *
     * @subgroup Quản trị
     */
    public function resetPassword(int $id, UserResetPasswordRequest $request)
    {
        $user = $this->users->resetPassword($id, (string) $request->validated('password'));

        return ApiResponse::success(
            data: ['user' => new UserResource($user)],
            code: 'USER_RESET_PASSWORD_SUCCESS',
            message: 'Đặt lại mật khẩu thành công',
        );
    }

    /**
     * Xoá tài khoản (soft delete)
     *
     * @subgroup Quản trị
     */
    public function destroy(int $id)
    {
        $this->users->delete($id);

        return ApiResponse::success(
            data: [],
            code: 'USER_DELETE_SUCCESS',
            message: 'Xoá tài khoản thành công',
        );
    }

    /**
     * Khôi phục tài khoản đã xoá
     *
     * @subgroup Quản trị
     */
    public function restore(int $id)
    {
        $user = $this->users->restore($id);

        return ApiResponse::success(
            data: ['user' => new UserResource($user)],
            code: 'USER_RESTORE_SUCCESS',
            message: 'Khôi phục tài khoản thành công',
        );
    }
}

