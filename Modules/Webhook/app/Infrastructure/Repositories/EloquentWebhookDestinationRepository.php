<?php

namespace Modules\Webhook\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Webhook\Domain\Models\WebhookDestination;
use Modules\Webhook\Infrastructure\Contracts\WebhookDestinationRepositoryInterface;
use Modules\Webhook\Infrastructure\Query\WebhookDestinationQueryConfig;

final class EloquentWebhookDestinationRepository implements WebhookDestinationRepositoryInterface
{
    public function paginateForWebhook(int $webhookId, ApiQueryParams $params): LengthAwarePaginator
    {
        $query = WebhookDestination::query()->where('webhook_id', $webhookId);

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: WebhookDestinationQueryConfig::allowedFilters(),
            allowedSorts: WebhookDestinationQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: WebhookDestinationQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function listActiveForWebhook(int $webhookId): Collection
    {
        return WebhookDestination::query()
            ->where('webhook_id', $webhookId)
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function findForWebhookOrFail(int $webhookId, int $destinationId): WebhookDestination
    {
        /** @var WebhookDestination $item */
        $item = WebhookDestination::query()
            ->where('webhook_id', $webhookId)
            ->findOrFail($destinationId);

        return $item;
    }
}

