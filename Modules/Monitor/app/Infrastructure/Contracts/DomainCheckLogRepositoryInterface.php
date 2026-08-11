<?php

namespace Modules\Monitor\Infrastructure\Contracts;

use Illuminate\Support\Collection;
use Modules\Monitor\Domain\Models\DomainCheckLog;

interface DomainCheckLogRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): DomainCheckLog;

    /**
     * @return Collection<int, DomainCheckLog>
     */
    public function listForDomain(int $domainId, int $limit = 20): Collection;
}