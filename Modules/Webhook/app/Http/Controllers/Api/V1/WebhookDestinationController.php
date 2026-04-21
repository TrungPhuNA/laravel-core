<?php

namespace Modules\Webhook\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Webhook\Application\Contracts\WebhookDestinationServiceInterface;
use Modules\Webhook\Http\Requests\Api\V1\WebhookDestinationIndexRequest;
use Modules\Webhook\Http\Requests\Api\V1\WebhookDestinationStoreRequest;
use Modules\Webhook\Http\Requests\Api\V1\WebhookDestinationUpdateRequest;
use Modules\Webhook\Http\Resources\Api\V1\WebhookDestinationResource;

/**
 * @group Webhook
 * @subgroup Destinations (Forward)
 */
final class WebhookDestinationController extends Controller
{
    public function __construct(
        private readonly WebhookDestinationServiceInterface $destinations,
    ) {}

    public function index(int $id, WebhookDestinationIndexRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $params = $request->apiQueryParams();
        $paginator = $this->destinations->paginateForUserWebhook((int) $user->id, $id, $params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: WebhookDestinationResource::collection($paginator->items()),
            code: 'WEBHOOK_DESTINATION_LIST_SUCCESS',
            message: 'Lấy danh sách điểm nhận thành công',
        );
    }

    public function store(int $id, WebhookDestinationStoreRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $dest = $this->destinations->createForUserWebhook((int) $user->id, $id, $request->validated());

        return ApiResponse::success(
            data: ['destination' => new WebhookDestinationResource($dest)],
            code: 'WEBHOOK_DESTINATION_CREATE_SUCCESS',
            message: 'Tạo điểm nhận thành công',
            status: 201,
        );
    }

    public function show(int $id, int $destinationId, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $dest = $this->destinations->getForUserWebhook((int) $user->id, $id, $destinationId);

        return ApiResponse::success(
            data: ['destination' => new WebhookDestinationResource($dest)],
            code: 'WEBHOOK_DESTINATION_SHOW_SUCCESS',
            message: 'Lấy điểm nhận thành công',
        );
    }

    public function update(int $id, int $destinationId, WebhookDestinationUpdateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $dest = $this->destinations->updateForUserWebhook((int) $user->id, $id, $destinationId, $request->validated());

        return ApiResponse::success(
            data: ['destination' => new WebhookDestinationResource($dest)],
            code: 'WEBHOOK_DESTINATION_UPDATE_SUCCESS',
            message: 'Cập nhật điểm nhận thành công',
        );
    }

    public function destroy(int $id, int $destinationId, \Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $this->destinations->deleteForUserWebhook((int) $user->id, $id, $destinationId);

        return ApiResponse::success(
            data: null,
            code: 'WEBHOOK_DESTINATION_DELETE_SUCCESS',
            message: 'Xoá điểm nhận thành công',
        );
    }
}

