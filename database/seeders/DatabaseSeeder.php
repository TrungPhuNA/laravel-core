<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed các tài khoản mặc định (admin, system...) để test nhanh.
        $this->call([
            AdminUserSeeder::class,
            DemoUsersSeeder::class,
            \Modules\Setting\Database\Seeders\SettingDatabaseSeeder::class,
            \Modules\Ecommerce\Database\Seeders\EcommerceDatabaseSeeder::class,
        ]);
    }
}
