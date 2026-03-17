<?php

namespace Modules\Auth\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Application\Contracts\AuthServiceInterface;
use Modules\Auth\Http\Requests\Api\V1\LoginRequest;
use Modules\Auth\Http\Requests\Api\V1\RegisterRequest;
use Modules\Auth\Http\Requests\Api\V1\UpdateProfileRequest;
use Modules\Auth\Http\Resources\Api\V1\UserResource;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $auth,
    ) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->auth->register($request->validated());

        return ApiResponse::ok([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], status: 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->auth->login($request->validated());

        return ApiResponse::ok([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function me()
    {
        return ApiResponse::ok([
            'user' => new UserResource($this->user()),
        ]);
    }

    public function logout()
    {
        $this->auth->logout($this->user());

        return ApiResponse::ok(null);
    }

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
