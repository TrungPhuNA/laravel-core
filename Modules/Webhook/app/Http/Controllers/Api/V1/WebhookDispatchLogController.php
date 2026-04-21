<?php

namespace Modules\Webhook\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Webhook\Application\Contracts\WebhookDispatchLogServiceInterface;
use Modules\Webhook\Http\Requests\Api\V1\WebhookDispatchLogIndexRequest;
use Modules\Webhook\Http\Resources\Api\V1\WebhookDispatchLogResource;

/**
 * @group Webhook
 * @subgroup Dispatch Logs
 */
final class WebhookDispatchLogController extends Controller
{
    public function __construct(
        private readonly WebhookDispatchLogServiceInterface $dispatches,
    ) {}

    public function index(int $id, WebhookDispatchLogIndexRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $params = $request->apiQueryParams();
        $paginator = $this->dispatches->paginateForUserWebhook((int) $user->id, $id, $params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: WebhookDispatchLogResource::collection($paginator->items()),
            code: 'WEBHOOK_DISPATCH_LOG_LIST_SUCCESS',
            message: 'Lấy log bắn webhook thành công',
        );
    }

    public function show(int $id, int $dispatchId, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $item = $this->dispatches->getForUserWebhook((int) $user->id, $id, $dispatchId);

        return ApiResponse::success(
            data: [
                'dispatch' => [
                    'id' => (int) $item->id,
                    'webhook_id' => (int) $item->webhook_id,
                    'webhook_request_id' => (int) $item->webhook_request_id,
                    'destination_id' => (int) $item->destination_id,
                    'status' => $item->status,
                    'dispatched_at' => $item->dispatched_at?->toISOString(),
                    'duration_ms' => $item->duration_ms,
                    'request_body' => $item->request_body,
                    'response_status' => $item->response_status,
                    'response_headers' => $item->response_headers,
                    'response_body' => $item->response_body,
                    'error_type' => $item->error_type,
                    'error_message' => $item->error_message,
                    'created_at' => $item->created_at?->toISOString(),
                    'updated_at' => $item->updated_at?->toISOString(),
                ],
            ],
            code: 'WEBHOOK_DISPATCH_LOG_SHOW_SUCCESS',
            message: 'Lấy log bắn webhook thành công',
        );
    }
}
