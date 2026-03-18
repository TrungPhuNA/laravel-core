<?php

namespace Database\Seeders;

use App\Core\Support\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Mặc định tạo 100 user để test list/filter/paginate.
        // Có thể override bằng env DEMO_USERS_COUNT=200
        $count = (int) env('DEMO_USERS_COUNT', 100);
        $count = max(0, min($count, 5000));

        if ($count === 0) {
            return;
        }

        User::factory()
            ->count($count)
            ->create([
                'user_type' => UserType::USER,
            ]);
    }
}

