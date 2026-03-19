<?php

namespace Modules\Webhook\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Webhook\Application\Contracts\WebhookServiceInterface;
use Modules\Webhook\Http\Requests\Api\V1\WebhookIndexRequest;
use Modules\Webhook\Http\Requests\Api\V1\WebhookStoreRequest;
use Modules\Webhook\Http\Requests\Api\V1\WebhookUpdateRequest;
use Modules\Webhook\Http\Resources\Api\V1\WebhookResource;

/**
 * @group Webhook
 * @subgroup Quản lý
 */
final class WebhookController extends Controller
{
    public function __construct(
        private readonly WebhookServiceInterface $webhooks,
    ) {}

    /**
     * Danh sách webhook của tôi
     */
    public function index(WebhookIndexRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $params = $request->apiQueryParams();
        $paginator = $this->webhooks->paginateForUser((int) $user->id, $params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: WebhookResource::collection($paginator->items()),
            code: 'WEBHOOK_LIST_SUCCESS',
            message: 'Lấy danh sách webhook thành công',
        );
    }

    /**
     * Tạo webhook
     */
    public function store(WebhookStoreRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $result = $this->webhooks->createForUser((int) $user->id, $request->validated());

        // auth_token chi tra ve 1 lan neu auth_type=token.
        return ApiResponse::success(
            data: [
                'webhook' => new WebhookResource($result['webhook']),
                'auth_token' => $result['auth_token'],
                'auth_secret' => $result['auth_secret'] ?? null,
                'receive_url' => url('/api/v1/webhooks/receive/'.$result['webhook']->public_id),
            ],
            code: 'WEBHOOK_CREATE_SUCCESS',
            message: 'Tạo webhook thành công',
            status: 201,
        );
    }

    /**
     * Chi tiết webhook
     */
    public function show(int $id, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $webhook = $this->webhooks->getForUser((int) $user->id, $id);

        return ApiResponse::success(
            data: ['webhook' => new WebhookResource($webhook)],
            code: 'WEBHOOK_SHOW_SUCCESS',
            message: 'Lấy webhook thành công',
        );
    }

    /**
     * Cập nhật webhook
     */
    public function update(int $id, WebhookUpdateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $result = $this->webhooks->updateForUser((int) $user->id, $id, $request->validated());

        return ApiResponse::success(
            data: [
                'webhook' => new WebhookResource($result['webhook']),
                'auth_token' => $result['auth_token'], // null neu khong rotate
                'auth_secret' => $result['auth_secret'] ?? null, // null neu khong rotate
                'receive_url' => url('/api/v1/webhooks/receive/'.$result['webhook']->public_id),
            ],
            code: 'WEBHOOK_UPDATE_SUCCESS',
            message: 'Cập nhật webhook thành công',
        );
    }

    /**
     * Xoá webhook
     */
    public function destroy(int $id, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $this->webhooks->deleteForUser((int) $user->id, $id);

        return ApiResponse::success(
            data: null,
            code: 'WEBHOOK_DELETE_SUCCESS',
            message: 'Xoá webhook thành công',
        );
    }

    /**
     * Rotate token (tạo token mới)
     */
    public function rotateToken(int $id, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $token = $this->webhooks->rotateToken((int) $user->id, $id);
        $webhook = $this->webhooks->getForUser((int) $user->id, $id);

        return ApiResponse::success(
            data: [
                'webhook' => new WebhookResource($webhook),
                'auth_token' => $token,
                'receive_url' => url('/api/v1/webhooks/receive/'.$webhook->public_id),
            ],
            code: 'WEBHOOK_ROTATE_TOKEN_SUCCESS',
            message: 'Đã tạo token mới',
        );
    }

    /**
     * Rotate secret HMAC (tạo secret mới)
     */
    public function rotateSecret(int $id, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Tận dụng updateForUser với rotate_secret=true
        $result = $this->webhooks->updateForUser((int) $user->id, $id, [
            'auth_type' => 'hmac',
            'rotate_secret' => true,
        ]);

        return ApiResponse::success(
            data: [
                'webhook' => new WebhookResource($result['webhook']),
                'auth_secret' => $result['auth_secret'] ?? null,
                'receive_url' => url('/api/v1/webhooks/receive/'.$result['webhook']->public_id),
            ],
            code: 'WEBHOOK_ROTATE_SECRET_SUCCESS',
            message: 'Đã tạo secret mới',
        );
    }
}
