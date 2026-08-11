<?php

namespace Modules\Monitor\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Monitor\Domain\Models\Domain;
use Modules\Monitor\Infrastructure\Contracts\DomainRepositoryInterface;
use Modules\Monitor\Infrastructure\Query\DomainQueryConfig;

final class EloquentDomainRepository implements DomainRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = Domain::query();

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: DomainQueryConfig::allowedFilters(),
            allowedSorts: DomainQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: DomainQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function create(array $data): Domain
    {
        /** @var Domain $domain */
        $domain = Domain::query()->create($data);

        return $domain;
    }

    public function update(Domain $domain, array $data): Domain
    {
        $domain->fill($data);
        $domain->save();

        return $domain;
    }

    public function delete(Domain $domain): void
    {
        $domain->delete();
    }

    public function findOrFail(int $id): Domain
    {
        /** @var Domain $domain */
        $domain = Domain::query()->findOrFail($id);

        return $domain;
    }

    public function findByDomain(string $domain): ?Domain
    {
        /** @var Domain|null $found */
        $found = Domain::query()->where('domain', $domain)->first();

        return $found;
    }

    public function listActive(): Collection
    {
        return Domain::query()->where('is_active', true)->get();
    }
}