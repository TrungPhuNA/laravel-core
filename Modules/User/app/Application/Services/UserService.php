<?php

namespace Modules\User\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\Query\ApiQueryParams;
use App\Core\Support\UserType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\User\Application\Contracts\UserServiceInterface;
use Modules\User\Infrastructure\Contracts\UserRepositoryInterface;

final class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->users->paginate($params);
    }

    public function getById(int $id): User
    {
        return $this->users->findOrFail($id);
    }

    public function create(array $input): User
    {
        $email = Str::lower(trim((string) ($input['email'] ?? '')));
        $input['email'] = $email;

        // Đảm bảo không tạo trùng email.
        if (User::query()->where('email', $email)->exists()) {
            throw ApiException::unprocessable(ErrorCode::EMAIL_ALREADY_EXISTS->value, 'Email already exists');
        }

        // Chuẩn hoá user_type (nếu có).
        if (isset($input['user_type'])) {
            $input['user_type'] = $this->normalizeUserType((string) $input['user_type']);
        } else {
            $input['user_type'] = UserType::USER;
        }

        return DB::transaction(function () use ($input) {
            return $this->users->create($input);
        });
    }

    public function update(int $id, array $input): User
    {
        $user = $this->users->findOrFail($id);

        if (isset($input['email'])) {
            $email = Str::lower(trim((string) $input['email']));
            $input['email'] = $email;

            if (User::query()->where('email', $email)->where('id', '!=', $user->id)->exists()) {
                throw ApiException::unprocessable(ErrorCode::EMAIL_ALREADY_EXISTS->value, 'Email already exists');
            }
        }

        if (isset($input['user_type'])) {
            $input['user_type'] = $this->normalizeUserType((string) $input['user_type']);
        }

        return DB::transaction(function () use ($user, $input) {
            return $this->users->update($user, $input);
        });
    }

    public function updateUserType(int $id, string $userType): User
    {
        $user = $this->users->findOrFail($id);

        $type = $this->normalizeUserType($userType);

        return DB::transaction(function () use ($user, $type) {
            return $this->users->update($user, ['user_type' => $type]);
        });
    }

    public function resetPassword(int $id, string $password): User
    {
        $user = $this->users->findOrFail($id);

        return DB::transaction(function () use ($user, $password) {
            return $this->users->update($user, ['password' => $password]);
        });
    }

    public function delete(int $id): void
    {
        $user = $this->users->findOrFail($id);

        DB::transaction(function () use ($user) {
            $this->users->delete($user);
        });
    }

    public function restore(int $id): User
    {
        $user = $this->users->findWithTrashedOrFail($id);

        return DB::transaction(function () use ($user) {
            return $this->users->restore($user);
        });
    }

    private function normalizeUserType(string $value): UserType
    {
        $value = strtoupper(trim($value));

        foreach (UserType::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        throw ApiException::unprocessable(
            ErrorCode::VALIDATION_ERROR->value,
            __('messages.validation_error'),
            ['user_type' => [__('messages.invalid_user_type')]],
        );
    }
}
