<?php

namespace Modules\Webhook\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\WebhookDispatchLog;
use Modules\Webhook\Infrastructure\Contracts\WebhookDispatchLogRepositoryInterface;
use Modules\Webhook\Infrastructure\Query\WebhookDispatchLogQueryConfig;

final class EloquentWebhookDispatchLogRepository implements WebhookDispatchLogRepositoryInterface
{
    public function paginateForWebhook(int $webhookId, ApiQueryParams $params): LengthAwarePaginator
    {
        $query = WebhookDispatchLog::query()
            ->where('webhook_id', $webhookId);

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: WebhookDispatchLogQueryConfig::allowedFilters(),
            allowedSorts: WebhookDispatchLogQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: WebhookDispatchLogQueryConfig::defaultSorts(),
        );

        return $query->paginate(perPage: $params->perPage, page: $params->page);
    }

    public function findForWebhookOrFail(int $webhookId, int $dispatchLogId): WebhookDispatchLog
    {
        /** @var WebhookDispatchLog $item */
        $item = WebhookDispatchLog::query()
            ->where('webhook_id', $webhookId)
            ->findOrFail($dispatchLogId);

        return $item;
    }
}

