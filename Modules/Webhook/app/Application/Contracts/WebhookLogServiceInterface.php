<?php

namespace Modules\Webhook\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\WebhookRequest;

interface WebhookLogServiceInterface
{
    public function paginateForUserWebhook(int $userId, int $webhookId, ApiQueryParams $params): LengthAwarePaginator;

    public function getForUserWebhook(int $userId, int $webhookId, int $requestId): WebhookRequest;

    public function pruneForUserWebhook(int $userId, int $webhookId, \DateTimeInterface $before): int;

    public function getStatsForUserWebhook(int $userId, int $webhookId, \DateTimeInterface $since): array;
}

