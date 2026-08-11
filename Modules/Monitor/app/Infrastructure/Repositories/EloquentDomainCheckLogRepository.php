<?php

namespace Modules\Monitor\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Modules\Monitor\Domain\Models\DomainCheckLog;
use Modules\Monitor\Infrastructure\Contracts\DomainCheckLogRepositoryInterface;

final class EloquentDomainCheckLogRepository implements DomainCheckLogRepositoryInterface
{
    public function create(array $data): DomainCheckLog
    {
        /** @var DomainCheckLog $log */
        $log = DomainCheckLog::query()->create($data);

        return $log;
    }

    public function listForDomain(int $domainId, int $limit = 20): Collection
    {
        return DomainCheckLog::query()
            ->where('domain_id', $domainId)
            ->orderByDesc('checked_at')
            ->limit($limit)
            ->get();
    }
}