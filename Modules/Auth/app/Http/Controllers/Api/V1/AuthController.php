<?php

namespace Modules\Auth\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Application\Contracts\AuthServiceInterface;
use Modules\Auth\Http\Requests\Api\V1\LoginRequest;
use Modules\Auth\Http\Requests\Api\V1\RegisterRequest;
use Modules\Auth\Http\Requests\Api\V1\UpdateProfileRequest;
use Modules\Auth\Http\Resources\Api\V1\UserResource;

/**
 * @group Xác thực
 *
 * API đăng ký/đăng nhập/cập nhật profile sử dụng Bearer token (Sanctum).
 */
final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $auth,
    ) {}

    /**
     * Đăng ký
     *
     * @subgroup Tài khoản
     * @subgroupDescription Các thao tác đăng ký/đăng nhập.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {
        $result = $this->auth->register($request->validated());

        return ApiResponse::ok([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], status: 201);
    }

    /**
     * Đăng nhập
     *
     * @subgroup Tài khoản
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $result = $this->auth->login($request->validated());

        return ApiResponse::ok([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    /**
     * Thông tin tài khoản
     *
     * Lấy thông tin user đang đăng nhập.
     *
     * @subgroup Hồ sơ
     * @subgroupDescription Các thao tác xem/cập nhật hồ sơ.
     *
     * @authenticated
     */
    public function me()
    {
        return ApiResponse::ok([
            'user' => new UserResource($this->user()),
        ]);
    }

    /**
     * Đăng xuất
     *
     * Thu hồi token hiện tại.
     *
     * @subgroup Phiên
     * @subgroupDescription Quản lý phiên và token.
     *
     * @authenticated
     */
    public function logout()
    {
        $this->auth->logout($this->user());

        return ApiResponse::ok(null);
    }

    /**
     * Cập nhật profile
     *
     * Cập nhật thông tin profile của user hiện tại.
     *
     * @subgroup Hồ sơ
     *
     * @authenticated
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->auth->updateProfile($this->user(), $request->validated());

        return ApiResponse::ok([
            'user' => new UserResource($user),
        ]);
    }

    private function user()
    {
        /** @var \App\Models\User $user */
        $user = request()->user();

        return $user;
    }
}
