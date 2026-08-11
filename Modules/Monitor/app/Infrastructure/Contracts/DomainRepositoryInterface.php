<?php

namespace Modules\Monitor\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Monitor\Domain\Models\Domain;

interface DomainRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Domain;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Domain $domain, array $data): Domain;

    public function delete(Domain $domain): void;

    public function findOrFail(int $id): Domain;

    public function findByDomain(string $domain): ?Domain;

    /**
     * @return Collection<int, Domain>
     */
    public function listActive(): Collection;
}