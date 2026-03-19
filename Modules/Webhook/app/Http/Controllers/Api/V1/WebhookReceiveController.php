<?php

namespace Modules\Webhook\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Webhook\Application\Contracts\WebhookReceiverServiceInterface;

/**
 * @group Webhook
 * @subgroup Receiver (Public)
 */
final class WebhookReceiveController extends Controller
{
    public function __construct(
        private readonly WebhookReceiverServiceInterface $receiver,
    ) {}

    /**
     * Nhận request từ bên ngoài
     *
     * Endpoint public. Webhook có thể cấu hình:
     * - Cho phép GET/POST
     * - Có token hoặc không
     * - Validate params theo rule
     *
     * @unauthenticated
     */
    public function handle(string $publicId, Request $request)
    {
        $result = $this->receiver->receive($publicId, $request);

        return ApiResponse::success(
            data: [
                'received' => true,
                'webhook_public_id' => $result['webhook']->public_id,
                'validated' => $result['validated'],
            ],
            code: 'WEBHOOK_RECEIVED',
            message: 'Đã nhận webhook',
        );
    }
}

