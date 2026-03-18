<?php

namespace Modules\Setting\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Setting\Domain\Models\QueueFailedJob;
use Modules\Setting\Domain\Models\QueueJob;
use Modules\Setting\Domain\Models\QueueJobBatch;

interface QueueRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function stats(): array;

    public function paginateJobs(ApiQueryParams $params): LengthAwarePaginator;

    public function findJobOrFail(int $id): QueueJob;

    public function paginateFailedJobs(ApiQueryParams $params): LengthAwarePaginator;

    public function findFailedJobOrFail(int $id): QueueFailedJob;

    public function retryFailedJob(int $id): void;

    public function forgetFailedJob(int $id): void;

    public function paginateBatches(ApiQueryParams $params): LengthAwarePaginator;

    public function findBatchOrFail(string $id): QueueJobBatch;
}

