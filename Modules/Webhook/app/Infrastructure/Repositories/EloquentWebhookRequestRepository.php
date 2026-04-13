<?php

namespace Modules\Webhook\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\WebhookRequest;
use Modules\Webhook\Infrastructure\Contracts\WebhookRequestRepositoryInterface;
use Modules\Webhook\Infrastructure\Query\WebhookRequestQueryConfig;

final class EloquentWebhookRequestRepository implements WebhookRequestRepositoryInterface
{
    public function paginateForWebhook(int $webhookId, ApiQueryParams $params): LengthAwarePaginator
    {
        $query = WebhookRequest::query()->where('webhook_id', $webhookId);

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: WebhookRequestQueryConfig::allowedFilters(),
            allowedSorts: WebhookRequestQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: WebhookRequestQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function findForWebhookOrFail(int $webhookId, int $requestId): WebhookRequest
    {
        /** @var WebhookRequest $item */
        $item = WebhookRequest::query()
            ->where('webhook_id', $webhookId)
            ->findOrFail($requestId);

        return $item;
    }

    public function pruneBefore(int $webhookId, \DateTimeInterface $before): int
    {
        return WebhookRequest::query()
            ->where('webhook_id', $webhookId)
            ->where('received_at', '<', $before)
            ->delete();
    }

    public function getStats(int $webhookId, \DateTimeInterface $since): array
    {
        $stats = WebhookRequest::query()
            ->where('webhook_id', $webhookId)
            ->where('received_at', '>=', $since)
            ->selectRaw('DATE(received_at) as date')
            ->selectRaw("COUNT(CASE WHEN status = 'success' THEN 1 END) as success_count")
            ->selectRaw("COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_count")
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return $stats->toArray();
    }
}

