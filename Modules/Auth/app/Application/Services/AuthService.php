<?php

namespace Modules\Auth\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Auth\Application\Contracts\AuthServiceInterface;

final class AuthService implements AuthServiceInterface
{
    public function register(array $input): array
    {
        $email = Str::lower(trim((string) $input['email']));

        if (User::query()->where('email', $email)->exists()) {
            throw ApiException::unprocessable(ErrorCode::EMAIL_ALREADY_EXISTS->value, 'Email already exists');
        }

        // For security: user_type is not accepted from public register endpoint.
        $user = User::create([
            'name' => $input['name'],
            'email' => $email,
            'password' => $input['password'],
            'user_type' => UserType::USER,
        ]);

        $token = $user->createToken($input['device_name'] ?? 'api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $input): array
    {
        $email = Str::lower(trim((string) $input['email']));
        $user = User::query()->where('email', $email)->first();

        if (!$user || !Hash::check((string) $input['password'], (string) $user->password)) {
            throw ApiException::unprocessable(ErrorCode::INVALID_CREDENTIALS->value, 'Invalid credentials');
        }

        $token = $user->createToken($input['device_name'] ?? 'api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function updateProfile(User $user, array $input): User
    {
        $user->fill($input);
        $user->save();

        return $user->refresh();
    }
}

