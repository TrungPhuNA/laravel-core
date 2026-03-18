<?php

namespace App\Core\Infrastructure\Repository;

use App\Core\Infrastructure\Repository\Contracts\RepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Decorator cache đơn giản cho repository (cache theo id).
 *
 * Lưu ý:
 * - Đây là building block. Với các use-case phức tạp (list cache, tag, invalidation theo quan hệ)
 *   bạn nên viết Cached*Repository riêng trong module.
 */
abstract class CachedRepository implements RepositoryInterface
{
    public function __construct(
        protected readonly CacheRepository $cache,
        protected readonly RepositoryInterface $inner,
    ) {}

    /**
     * Prefix cache key, ví dụ: "product".
     */
    abstract protected function cachePrefix(): string;

    /**
     * TTL (giây).
     */
    protected function ttlSeconds(): int
    {
        return 60;
    }

    protected function keyForId(mixed $id): string
    {
        return $this->cachePrefix().':'.$id;
    }

    public function find(mixed $id): ?Model
    {
        $key = $this->keyForId($id);

        return $this->cache->remember($key, $this->ttlSeconds(), function () use ($id) {
            return $this->inner->find($id);
        });
    }

    public function findOrFail(mixed $id): Model
    {
        $model = $this->find($id);

        if (!$model) {
            return $this->inner->findOrFail($id);
        }

        return $model;
    }

    public function forgetById(mixed $id): void
    {
        $this->cache->forget($this->keyForId($id));
    }
}

