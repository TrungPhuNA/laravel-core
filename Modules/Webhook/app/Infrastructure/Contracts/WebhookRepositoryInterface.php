<?php

namespace Modules\Webhook\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\Webhook;

interface WebhookRepositoryInterface
{
    public function paginateForUser(int $userId, ApiQueryParams $params): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Webhook;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Webhook $webhook, array $data): Webhook;

    public function delete(Webhook $webhook): void;

    public function findForUserOrFail(int $id, int $userId): Webhook;

    public function findByPublicIdOrFail(string $publicId): Webhook;
}

