<?php

namespace Modules\Ecommerce\Database\Seeders;

use App\Models\User;
use App\Core\Support\UserType;
use Illuminate\Database\Seeder;
use Modules\Ecommerce\Domain\Models\Shop;

final class ShopSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Shop $shop */
        $shop = Shop::query()->firstOrCreate(
            ['code' => 'DEFAULT'],
            [
                'name' => 'Default Shop',
                'timezone' => 'Asia/Ho_Chi_Minh',
                'currency' => 'VND',
                'is_active' => true,
                'meta' => ['seeded_at' => now()->toISOString()],
            ],
        );

        // Auto-attach ADMIN/SYSTEM users so project can test right away.
        $users = User::query()
            ->whereIn('user_type', [UserType::ADMIN, UserType::SYSTEM])
            ->get();

        if ($users->isNotEmpty()) {
            $shop->users()->syncWithoutDetaching(
                $users->mapWithKeys(fn (User $u) => [(int) $u->id => ['role' => 'ADMIN']])->all(),
            );
        }
    }
}

