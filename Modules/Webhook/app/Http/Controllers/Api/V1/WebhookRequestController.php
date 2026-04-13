<?php

namespace Modules\Webhook\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Modules\Webhook\Application\Contracts\WebhookLogServiceInterface;
use Modules\Webhook\Http\Requests\Api\V1\WebhookRequestIndexRequest;
use Modules\Webhook\Http\Requests\Api\V1\WebhookRequestPruneRequest;
use Modules\Webhook\Http\Resources\Api\V1\WebhookRequestResource;

/**
 * @group Webhook
 * @subgroup Logs
 */
final class WebhookRequestController extends Controller
{
    public function __construct(
        private readonly WebhookLogServiceInterface $logs,
    ) {}

    /**
     * Danh sách log request của 1 webhook
     */
    public function index(int $id, WebhookRequestIndexRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $params = $request->apiQueryParams();
        $paginator = $this->logs->paginateForUserWebhook((int) $user->id, $id, $params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: WebhookRequestResource::collection($paginator->items()),
            code: 'WEBHOOK_REQUEST_LOG_LIST_SUCCESS',
            message: 'Lấy log requests thành công',
        );
    }

    /**
     * Chi tiết 1 log request
     */
    public function show(int $id, int $requestId, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $item = $this->logs->getForUserWebhook((int) $user->id, $id, $requestId);

        return ApiResponse::success(
            data: [
                'request' => [
                    'id' => $item->id,
                    'webhook_id' => (int) $item->webhook_id,
                    'method' => $item->method,
                    'ip' => $item->ip,
                    'headers' => $item->headers,
                    'query' => $item->query,
                    'body' => $item->body,
                    'received_at' => $item->received_at?->toISOString(),
                    'created_at' => $item->created_at?->toISOString(),
                ],
            ],
            code: 'WEBHOOK_REQUEST_LOG_SHOW_SUCCESS',
            message: 'Lấy log request thành công',
        );
    }

    /**
     * Prune log request (xoá log cũ)
     */
    public function prune(int $id, WebhookRequestPruneRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $days = $request->validated('days');
        $before = $request->validated('before');

        if ($days !== null) {
            $beforeDt = now()->subDays((int) $days);
        } else {
            $beforeDt = $before ? Carbon::parse($before) : now()->subDays(30);
        }

        $deleted = $this->logs->pruneForUserWebhook((int) $user->id, $id, $beforeDt);

        return ApiResponse::success(
            data: ['deleted' => $deleted, 'before' => $beforeDt->toISOString()],
            code: 'WEBHOOK_REQUEST_LOG_PRUNE_SUCCESS',
            message: 'Đã xoá log cũ',
        );
    }

    /**
     * Thống kê log request theo ngày
     */
    public function stats(int $id, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Mac dinh lay 30 ngay qua.
        $days = (int) $request->query('days', 30);
        $since = now()->subDays($days)->startOfDay();

        $data = $this->logs->getStatsForUserWebhook((int) $user->id, $id, $since);

        return ApiResponse::success(
            data: [
                'stats' => $data,
                'period_days' => $days,
                'since' => $since->toISOString(),
            ],
            code: 'WEBHOOK_REQUEST_LOG_STATS_SUCCESS',
            message: 'Lấy thống kê log thành công',
        );
    }
}
