<?php

namespace Modules\Webhook\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Application\Contracts\WebhookLogServiceInterface;
use Modules\Webhook\Domain\Models\WebhookRequest;
use Modules\Webhook\Infrastructure\Contracts\WebhookRepositoryInterface;
use Modules\Webhook\Infrastructure\Contracts\WebhookRequestRepositoryInterface;

final class WebhookLogService implements WebhookLogServiceInterface
{
    public function __construct(
        private readonly WebhookRepositoryInterface $webhooks,
        private readonly WebhookRequestRepositoryInterface $requests,
    ) {}

    public function paginateForUserWebhook(int $userId, int $webhookId, ApiQueryParams $params): LengthAwarePaginator
    {
        // Ensure webhook belongs to user.
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->requests->paginateForWebhook((int) $webhook->id, $params);
    }

    public function getForUserWebhook(int $userId, int $webhookId, int $requestId): WebhookRequest
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->requests->findForWebhookOrFail((int) $webhook->id, $requestId);
    }

    public function pruneForUserWebhook(int $userId, int $webhookId, \DateTimeInterface $before): int
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->requests->pruneBefore((int) $webhook->id, $before);
    }

    public function getStatsForUserWebhook(int $userId, int $webhookId, \DateTimeInterface $since): array
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->requests->getStats((int) $webhook->id, $since);
    }
}

