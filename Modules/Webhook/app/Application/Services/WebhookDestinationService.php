<?php

namespace Modules\Webhook\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Webhook\Application\Contracts\WebhookDestinationServiceInterface;
use Modules\Webhook\Domain\Models\WebhookDestination;
use Modules\Webhook\Infrastructure\Contracts\WebhookDestinationRepositoryInterface;
use Modules\Webhook\Infrastructure\Contracts\WebhookRepositoryInterface;

final class WebhookDestinationService implements WebhookDestinationServiceInterface
{
    public function __construct(
        private readonly WebhookRepositoryInterface $webhooks,
        private readonly WebhookDestinationRepositoryInterface $destinations,
    ) {}

    public function paginateForUserWebhook(int $userId, int $webhookId, ApiQueryParams $params): LengthAwarePaginator
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->destinations->paginateForWebhook((int) $webhook->id, $params);
    }

    public function createForUserWebhook(int $userId, int $webhookId, array $data): WebhookDestination
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        /** @var WebhookDestination $dest */
        $dest = WebhookDestination::query()->create([
            'webhook_id' => (int) $webhook->id,
            'name' => (string) ($data['name'] ?? ''),
            'url' => (string) ($data['url'] ?? ''),
            'http_method' => strtoupper((string) ($data['http_method'] ?? 'POST')),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'headers' => $data['headers'] ?? null,
            'send_mode' => (string) ($data['send_mode'] ?? 'merge'),
            'field_mappings' => $data['field_mappings'] ?? null,
            'drop_mapped_sources' => (bool) ($data['drop_mapped_sources'] ?? false),
            'timeout_seconds' => (int) ($data['timeout_seconds'] ?? 10),
            'type' => (string) ($data['type'] ?? 'default'),
        ]);

        return $dest;
    }

    public function getForUserWebhook(int $userId, int $webhookId, int $destinationId): WebhookDestination
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);

        return $this->destinations->findForWebhookOrFail((int) $webhook->id, $destinationId);
    }

    public function updateForUserWebhook(int $userId, int $webhookId, int $destinationId, array $data): WebhookDestination
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);
        $dest = $this->destinations->findForWebhookOrFail((int) $webhook->id, $destinationId);

        $dest->forceFill([
            'name' => array_key_exists('name', $data) ? (string) $data['name'] : $dest->name,
            'url' => array_key_exists('url', $data) ? (string) $data['url'] : $dest->url,
            'http_method' => array_key_exists('http_method', $data) ? strtoupper((string) $data['http_method']) : $dest->http_method,
            'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : $dest->is_active,
            'headers' => array_key_exists('headers', $data) ? ($data['headers'] ?? null) : $dest->headers,
            'send_mode' => array_key_exists('send_mode', $data) ? (string) $data['send_mode'] : $dest->send_mode,
            'field_mappings' => array_key_exists('field_mappings', $data) ? ($data['field_mappings'] ?? null) : $dest->field_mappings,
            'drop_mapped_sources' => array_key_exists('drop_mapped_sources', $data) ? (bool) $data['drop_mapped_sources'] : $dest->drop_mapped_sources,
            'timeout_seconds' => array_key_exists('timeout_seconds', $data) ? (int) $data['timeout_seconds'] : $dest->timeout_seconds,
            'type' => array_key_exists('type', $data) ? (string) $data['type'] : $dest->type,
        ])->save();

        return $dest;
    }

    public function deleteForUserWebhook(int $userId, int $webhookId, int $destinationId): void
    {
        $webhook = $this->webhooks->findForUserOrFail($webhookId, $userId);
        $dest = $this->destinations->findForWebhookOrFail((int) $webhook->id, $destinationId);
        $dest->delete();
    }
}

