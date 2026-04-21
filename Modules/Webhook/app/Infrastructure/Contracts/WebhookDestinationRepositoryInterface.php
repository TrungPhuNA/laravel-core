<?php

namespace Modules\Webhook\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Webhook\Domain\Models\WebhookDestination;

interface WebhookDestinationRepositoryInterface
{
    public function paginateForWebhook(int $webhookId, ApiQueryParams $params): LengthAwarePaginator;

    /**
     * @return Collection<int, WebhookDestination>
     */
    public function listActiveForWebhook(int $webhookId): Collection;

    public function findForWebhookOrFail(int $webhookId, int $destinationId): WebhookDestination;
}

