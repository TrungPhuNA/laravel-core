<?php

namespace Modules\Webhook\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\WebhookDispatchLog;

interface WebhookDispatchLogRepositoryInterface
{
    public function paginateForWebhook(int $webhookId, ApiQueryParams $params): LengthAwarePaginator;

    public function findForWebhookOrFail(int $webhookId, int $dispatchLogId): WebhookDispatchLog;
    public function getStats(int $webhookId, \DateTimeInterface $since): array;
}

