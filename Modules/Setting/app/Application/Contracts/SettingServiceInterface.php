<?php

namespace Modules\Setting\Application\Contracts;

use Illuminate\Support\Collection;

interface SettingServiceInterface
{
    /**
     * @return Collection<int, \Modules\Setting\Domain\Models\Setting>
     */
    public function listPublic(): Collection;

    /**
     * @return Collection<int, \Modules\Setting\Domain\Models\Setting>
     */
    public function listAll(): Collection;

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function upsert(array $items, int $updatedById): void;

    public function getPublicByKey(string $key);

    public function getByKey(string $key);
}
