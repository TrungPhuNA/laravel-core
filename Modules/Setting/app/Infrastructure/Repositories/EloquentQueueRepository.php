<?php

namespace Modules\Setting\Infrastructure\Repositories;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Modules\Setting\Domain\Models\QueueFailedJob;
use Modules\Setting\Domain\Models\QueueJob;
use Modules\Setting\Domain\Models\QueueJobBatch;
use Modules\Setting\Infrastructure\Contracts\QueueRepositoryInterface;
use Modules\Setting\Infrastructure\Query\QueueBatchQueryConfig;
use Modules\Setting\Infrastructure\Query\QueueFailedJobQueryConfig;
use Modules\Setting\Infrastructure\Query\QueueJobQueryConfig;

final class EloquentQueueRepository implements QueueRepositoryInterface
{
    public function stats(): array
    {
        $now = time();

        $reserved = QueueJob::query()->whereNotNull('reserved_at')->count();
        $delayed = QueueJob::query()->whereNull('reserved_at')->where('available_at', '>', $now)->count();
        $pending = QueueJob::query()->whereNull('reserved_at')->where('available_at', '<=', $now)->count();

        return [
            'jobs' => [
                'pending' => $pending,
                'reserved' => $reserved,
                'delayed' => $delayed,
                'total' => $pending + $reserved + $delayed,
            ],
            'failed_jobs' => [
                'total' => QueueFailedJob::query()->count(),
            ],
            'batches' => [
                'total' => QueueJobBatch::query()->count(),
            ],
        ];
    }

    public function paginateJobs(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = QueueJob::query();

        $status = $params->filters['status'] ?? null;
        if (is_string($status) && $status !== '') {
            $now = time();

            if ($status === 'pending') {
                $query->whereNull('reserved_at')->where('available_at', '<=', $now);
            } elseif ($status === 'reserved') {
                $query->whereNotNull('reserved_at');
            } elseif ($status === 'delayed') {
                $query->whereNull('reserved_at')->where('available_at', '>', $now);
            } elseif ($status === 'all') {
                // no-op
            }
        }

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: QueueJobQueryConfig::allowedFilters(),
            allowedSorts: QueueJobQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: QueueJobQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function findJobOrFail(int $id): QueueJob
    {
        /** @var QueueJob $job */
        $job = QueueJob::query()->findOrFail($id);

        return $job;
    }

    public function paginateFailedJobs(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = QueueFailedJob::query();

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: QueueFailedJobQueryConfig::allowedFilters(),
            allowedSorts: QueueFailedJobQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: QueueFailedJobQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function findFailedJobOrFail(int $id): QueueFailedJob
    {
        /** @var QueueFailedJob $job */
        $job = QueueFailedJob::query()->findOrFail($id);

        return $job;
    }

    public function retryFailedJob(int $id): void
    {
        $code = Artisan::call('queue:retry', ['id' => [$id]]);

        if ($code !== 0) {
            throw new ApiException(
                errorCode: ErrorCode::INTERNAL_ERROR->value,
                message: 'Không thể retry failed job',
                status: 500,
                details: ['id' => $id],
            );
        }
    }

    public function forgetFailedJob(int $id): void
    {
        $code = Artisan::call('queue:forget', ['id' => $id]);

        if ($code !== 0) {
            throw new ApiException(
                errorCode: ErrorCode::INTERNAL_ERROR->value,
                message: 'Không thể xoá failed job',
                status: 500,
                details: ['id' => $id],
            );
        }
    }

    public function paginateBatches(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = QueueJobBatch::query();

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: QueueBatchQueryConfig::allowedFilters(),
            allowedSorts: QueueBatchQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: QueueBatchQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function findBatchOrFail(string $id): QueueJobBatch
    {
        /** @var QueueJobBatch $batch */
        $batch = QueueJobBatch::query()->findOrFail($id);

        return $batch;
    }
}

