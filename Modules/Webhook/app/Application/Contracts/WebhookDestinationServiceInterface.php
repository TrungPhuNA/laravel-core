<?php

namespace Modules\Webhook\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\WebhookDestination;

interface WebhookDestinationServiceInterface
{
    public function paginateForUserWebhook(int $userId, int $webhookId, ApiQueryParams $params): LengthAwarePaginator;

    public function createForUserWebhook(int $userId, int $webhookId, array $data): WebhookDestination;

    public function getForUserWebhook(int $userId, int $webhookId, int $destinationId): WebhookDestination;

    public function updateForUserWebhook(int $userId, int $webhookId, int $destinationId, array $data): WebhookDestination;

    public function deleteForUserWebhook(int $userId, int $webhookId, int $destinationId): void;
}

