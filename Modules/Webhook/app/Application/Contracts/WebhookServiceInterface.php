<?php

namespace Modules\Webhook\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Domain\Models\Webhook;

interface WebhookServiceInterface
{
    public function paginateForUser(int $userId, ApiQueryParams $params): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     * @return array{webhook: Webhook, auth_token: string|null, auth_secret?: string|null}
     */
    public function createForUser(int $userId, array $data): array;

    /**
     * @param array<string, mixed> $data
     * @return array{webhook: Webhook, auth_token: string|null, auth_secret?: string|null}
     */
    public function updateForUser(int $userId, int $id, array $data): array;

    public function getForUser(int $userId, int $id): Webhook;

    public function deleteForUser(int $userId, int $id): void;

    /**
     * Rotate token va tra ve token plain (chi hien thi 1 lan).
     */
    public function rotateToken(int $userId, int $id): string;
}
