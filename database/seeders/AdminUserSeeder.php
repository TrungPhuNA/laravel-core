<?php

namespace Database\Seeders;

use App\Core\Support\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: chạy nhiều lần không tạo trùng.
        // Dùng withTrashed để tránh lỗi unique email nếu user từng bị soft delete.
        $user = User::withTrashed()->updateOrCreate(
            ['email' => 'codethue94@gmail.com'],
            [
                'name' => 'Admin',
                'phone' => '0986420994',
                'password' => '123456789', // Model cast 'password' => 'hashed' sẽ tự hash
                'user_type' => UserType::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        if ($user->trashed()) {
            $user->restore();
        }
    }
}
