<?php

namespace Modules\Webhook\Application\Contracts;

interface WebhookForwarderServiceInterface
{
    /**
     * Dispatch payload to all active destinations of a webhook.
     *
     * @param array<string, mixed> $payload
     */
    public function dispatch(int $webhookId, int $webhookRequestId, array $payload): int;
}

