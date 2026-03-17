<?php

namespace Modules\Auth\Application\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    /**
     * @param array<string, mixed> $input
     * @return array{user: User, token: string}
     */
    public function register(array $input): array;

    /**
     * @param array<string, mixed> $input
     * @return array{user: User, token: string}
     */
    public function login(array $input): array;

    public function logout(User $user): void;

    /**
     * @param array<string, mixed> $input
     */
    public function updateProfile(User $user, array $input): User;
}

