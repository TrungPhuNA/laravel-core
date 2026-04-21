<?php

namespace Modules\Webhook\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\WebhookDispatchLog;

interface WebhookDispatchLogServiceInterface
{
    public function paginateForUserWebhook(int $userId, int $webhookId, ApiQueryParams $params): LengthAwarePaginator;

    public function getForUserWebhook(int $userId, int $webhookId, int $dispatchLogId): WebhookDispatchLog;
}

