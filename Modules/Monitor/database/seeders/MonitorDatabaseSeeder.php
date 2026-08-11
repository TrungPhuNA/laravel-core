<?php

namespace Modules\Monitor\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Monitor\Domain\Models\Domain;

class MonitorDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            ['domain' => 'vuatot.vn', 'note' => 'Domain chính'],
            ['domain' => '123code.net', 'note' => ''],
            ['domain' => 'devga.net', 'note' => ''],
            ['domain' => 'tilo.vn', 'note' => ''],
        ];

        foreach ($domains as $item) {
            Domain::query()->updateOrCreate(
                ['domain' => $item['domain']],
                [
                    'note' => $item['note'],
                    'is_active' => true,
                    'check_status' => 'unknown',
                ]
            );
        }
    }
}