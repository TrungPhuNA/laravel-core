<?php

namespace Modules\Setting\Application\Services;

use Illuminate\Support\Collection;
use Modules\Setting\Application\Contracts\SettingServiceInterface;
use Modules\Setting\Infrastructure\Contracts\SettingRepositoryInterface;

final class SettingService implements SettingServiceInterface
{
    public function __construct(
        private readonly SettingRepositoryInterface $settings,
    ) {}

    public function listPublic(): Collection
    {
        return $this->settings->all(true);
    }

    public function listAll(): Collection
    {
        return $this->settings->all(false);
    }

    public function upsert(array $items, int $updatedById): void
    {
        $this->settings->upsertMany($items, $updatedById);
    }

    public function getPublicByKey(string $key)
    {
        return $this->settings
            ->all(true)
            ->firstWhere('key', $key);
    }

    public function getByKey(string $key)
    {
        return $this->settings
            ->all(false)
            ->firstWhere('key', $key);
    }
}
