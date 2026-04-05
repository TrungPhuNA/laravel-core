<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;

class EcommerceDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShopSeeder::class,
            RbacSeeder::class,
        ]);
    }
}
