<?php

namespace Modules\Setting\Http\Controllers\Api\V1\Queue;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\QueueServiceInterface;
use Modules\Setting\Http\Requests\Api\V1\Queue\QueueBatchIndexRequest;
use Modules\Setting\Http\Resources\Api\V1\Queue\QueueBatchResource;

/**
 * @group Cài đặt
 * @subgroup Hàng đợi
 */
final class QueueBatchController extends Controller
{
    public function __construct(
        private readonly QueueServiceInterface $queue,
    ) {}

    /**
     * Danh sách job batches
     */
    public function index(QueueBatchIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $paginator = $this->queue->paginateBatches($params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: QueueBatchResource::collection($paginator->items()),
            code: 'QUEUE_BATCH_LIST_SUCCESS',
            message: 'Lấy danh sách batches thành công',
        );
    }

    /**
     * Chi tiết batch
     */
    public function show(string $id)
    {
        $batch = $this->queue->getBatchById($id);

        return ApiResponse::success(
            data: [
                'batch' => new QueueBatchResource($batch),
                'options' => $batch->options,
                'failed_job_ids' => $batch->failed_job_ids,
            ],
            code: 'QUEUE_BATCH_SHOW_SUCCESS',
            message: 'Lấy chi tiết batch thành công',
        );
    }
}

