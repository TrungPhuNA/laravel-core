<?php

namespace Modules\Webhook\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Webhook\Application\Contracts\WebhookForwarderServiceInterface;

final class DispatchWebhookDestinationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public readonly int $webhookId,
        public readonly int $webhookRequestId,
        public readonly array $payload,
    ) {}

    public function handle(WebhookForwarderServiceInterface $forwarder): void
    {
        $forwarder->dispatch($this->webhookId, $this->webhookRequestId, $this->payload);
    }
}

