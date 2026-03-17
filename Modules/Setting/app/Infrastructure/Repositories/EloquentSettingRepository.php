<?php

namespace Modules\Setting\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Modules\Setting\Domain\Models\Setting;
use Modules\Setting\Infrastructure\Contracts\SettingRepositoryInterface;

final class EloquentSettingRepository implements SettingRepositoryInterface
{
    public function all(bool $onlyPublic = false): Collection
    {
        $query = Setting::query()->orderBy('group')->orderBy('key');

        if ($onlyPublic) {
            $query->where('is_public', true);
        }

        return $query->get();
    }

    public function upsertMany(array $items, ?int $updatedById = null): void
    {
        $now = now();

        $rows = array_map(function (array $item) use ($updatedById, $now) {
            return [
                'key' => (string) $item['key'],
                'value' => array_key_exists('value', $item) ? json_encode($item['value']) : null,
                'group' => $item['group'] ?? null,
                'is_public' => (bool) ($item['is_public'] ?? false),
                'description' => $item['description'] ?? null,
                'updated_by' => $updatedById,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $items);

        Setting::query()->upsert(
            $rows,
            ['key'],
            ['value', 'group', 'is_public', 'description', 'updated_by', 'updated_at']
        );
    }

    public function clearCache(): void
    {
        // no-op
    }
}

