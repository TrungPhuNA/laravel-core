<?php

namespace Modules\Setting\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Setting\Application\Contracts\QueueServiceInterface;
use Modules\Setting\Domain\Models\QueueFailedJob;
use Modules\Setting\Domain\Models\QueueJob;
use Modules\Setting\Domain\Models\QueueJobBatch;
use Modules\Setting\Infrastructure\Contracts\QueueRepositoryInterface;

final class QueueService implements QueueServiceInterface
{
    public function __construct(
        private readonly QueueRepositoryInterface $queue,
    ) {}

    public function stats(): array
    {
        return $this->queue->stats();
    }

    public function paginateJobs(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->queue->paginateJobs($params);
    }

    public function getJobById(int $id): QueueJob
    {
        return $this->queue->findJobOrFail($id);
    }

    public function paginateFailedJobs(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->queue->paginateFailedJobs($params);
    }

    public function getFailedJobById(int $id): QueueFailedJob
    {
        return $this->queue->findFailedJobOrFail($id);
    }

    public function retryFailedJob(int $id): void
    {
        $this->queue->retryFailedJob($id);
    }

    public function forgetFailedJob(int $id): void
    {
        $this->queue->forgetFailedJob($id);
    }

    public function paginateBatches(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->queue->paginateBatches($params);
    }

    public function getBatchById(string $id): QueueJobBatch
    {
        return $this->queue->findBatchOrFail($id);
    }
}

