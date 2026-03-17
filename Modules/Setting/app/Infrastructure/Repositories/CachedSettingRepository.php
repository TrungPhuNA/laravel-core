<?php

namespace Modules\Setting\Infrastructure\Repositories;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;
use Modules\Setting\Infrastructure\Contracts\SettingRepositoryInterface;

final class CachedSettingRepository implements SettingRepositoryInterface
{
    private const CACHE_KEY_ALL = 'setting:all';
    private const CACHE_KEY_PUBLIC = 'setting:public';

    public function __construct(
        private readonly CacheRepository $cache,
        private readonly SettingRepositoryInterface $inner,
    ) {}

    public function all(bool $onlyPublic = false): Collection
    {
        $ttl = (int) config('setting.cache.ttl', 60);
        $key = $onlyPublic ? self::CACHE_KEY_PUBLIC : self::CACHE_KEY_ALL;

        return $this->cache->remember($key, $ttl, fn () => $this->inner->all($onlyPublic));
    }

    public function upsertMany(array $items, ?int $updatedById = null): void
    {
        $this->inner->upsertMany($items, $updatedById);
        $this->clearCache();
    }

    public function clearCache(): void
    {
        $this->cache->forget(self::CACHE_KEY_ALL);
        $this->cache->forget(self::CACHE_KEY_PUBLIC);
    }
}

