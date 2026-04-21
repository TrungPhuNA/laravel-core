<?php

namespace Modules\Webhook\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Application\Contracts\WebhookDispatchLogServiceInterface;
use Modules\Webhook\Domain\Models\WebhookDispatchLog;
use Modules\Webhook\Infrastructure\Contracts\WebhookDispatchLogRepositoryInterface;
use Modules\Webhook\Infrastructure\Contracts\WebhookRepositoryInterface;

final class WebhookDispatchLogService implements WebhookDispatchLogServiceInterface
{
    public function __construct(
        private readonly WebhookRepositoryInterface $webhooks,
        private readonly WebhookDispatchLogRepositoryInterface $dispatchLogs,
    ) {}

    public function paginateForUserWebhook(int $userId, int $webhookId, ApiQueryParams $params): LengthAwarePaginator
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->dispatchLogs->paginateForWebhook((int) $webhook->id, $params);
    }

    public function getForUserWebhook(int $userId, int $webhookId, int $dispatchLogId): WebhookDispatchLog
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->dispatchLogs->findForWebhookOrFail((int) $webhook->id, $dispatchLogId);
    }
}

