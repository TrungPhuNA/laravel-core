<?php

namespace App\Core\Infrastructure\Repository;

use App\Core\Infrastructure\Repository\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Eloquent repository để module reuse.
 *
 * Module nên tạo repository cụ thể và implement interface riêng (VD: ProductRepositoryInterface)
 * rồi có thể extend lớp này để khỏi viết lại logic cơ bản.
 */
abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    protected function query(): Builder
    {
        $class = $this->modelClass();

        /** @var Builder $query */
        $query = $class::query();

        return $query;
    }

    public function find(mixed $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findOrFail(mixed $id): Model
    {
        return $this->query()->findOrFail($id);
    }
}

