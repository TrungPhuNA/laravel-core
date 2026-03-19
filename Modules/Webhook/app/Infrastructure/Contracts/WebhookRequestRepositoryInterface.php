<?php

namespace Modules\Webhook\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\WebhookRequest;

interface WebhookRequestRepositoryInterface
{
    public function paginateForWebhook(int $webhookId, ApiQueryParams $params): LengthAwarePaginator;

    public function findForWebhookOrFail(int $webhookId, int $requestId): WebhookRequest;

    /**
     * Xoa log theo dieu kien.
     */
    public function pruneBefore(int $webhookId, \DateTimeInterface $before): int;
}

