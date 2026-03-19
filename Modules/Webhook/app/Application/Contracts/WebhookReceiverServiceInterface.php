<?php

namespace Modules\Webhook\Application\Contracts;

use Illuminate\Http\Request;
use Modules\Webhook\Domain\Models\Webhook;

interface WebhookReceiverServiceInterface
{
    /**
     * Xu ly request nhan vao webhook theo public_id.
     *
     * @return array{webhook: Webhook, validated: array<string, mixed>}
     */
    public function receive(string $publicId, Request $request): array;
}

