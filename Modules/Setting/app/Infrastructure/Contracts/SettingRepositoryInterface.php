<?php

namespace Modules\Setting\Infrastructure\Contracts;

use Illuminate\Support\Collection;

interface SettingRepositoryInterface
{
    /**
     * @return Collection<int, \Modules\Setting\Domain\Models\Setting>
     */
    public function all(bool $onlyPublic = false): Collection;

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function upsertMany(array $items, ?int $updatedById = null): void;

    public function clearCache(): void;
}

