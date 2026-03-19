<?php

namespace Modules\Webhook\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\Webhook;
use Modules\Webhook\Infrastructure\Contracts\WebhookRepositoryInterface;
use Modules\Webhook\Infrastructure\Query\WebhookQueryConfig;

final class EloquentWebhookRepository implements WebhookRepositoryInterface
{
    public function paginateForUser(int $userId, ApiQueryParams $params): LengthAwarePaginator
    {
        $query = Webhook::query()->where('user_id', $userId);

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: WebhookQueryConfig::allowedFilters(),
            allowedSorts: WebhookQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: WebhookQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function create(array $data): Webhook
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::query()->create($data);

        return $webhook;
    }

    public function update(Webhook $webhook, array $data): Webhook
    {
        $webhook->fill($data);
        $webhook->save();

        return $webhook;
    }

    public function delete(Webhook $webhook): void
    {
        $webhook->delete();
    }

    public function findForUserOrFail(int $id, int $userId): Webhook
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::query()
            ->where('user_id', $userId)
            ->findOrFail($id);

        return $webhook;
    }

    public function findByPublicIdOrFail(string $publicId): Webhook
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::query()->where('public_id', $publicId)->firstOrFail();

        return $webhook;
    }
}

