<?php

namespace Modules\Setting\Http\Controllers\Api\V1\Queue;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\QueueServiceInterface;
use Modules\Setting\Http\Requests\Api\V1\Queue\QueueFailedJobIndexRequest;
use Modules\Setting\Http\Resources\Api\V1\Queue\QueueFailedJobResource;

/**
 * @group Cài đặt
 * @subgroup Hàng đợi
 */
final class QueueFailedJobController extends Controller
{
    public function __construct(
        private readonly QueueServiceInterface $queue,
    ) {}

    /**
     * Danh sách failed jobs
     */
    public function index(QueueFailedJobIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $paginator = $this->queue->paginateFailedJobs($params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: QueueFailedJobResource::collection($paginator->items()),
            code: 'QUEUE_FAILED_LIST_SUCCESS',
            message: 'Lấy danh sách failed jobs thành công',
        );
    }

    /**
     * Chi tiết failed job
     */
    public function show(int $id)
    {
        $job = $this->queue->getFailedJobById($id);

        return ApiResponse::success(
            data: [
                'job' => new QueueFailedJobResource($job),
                'payload' => $job->payload,
                'exception' => $job->exception,
            ],
            code: 'QUEUE_FAILED_SHOW_SUCCESS',
            message: 'Lấy chi tiết failed job thành công',
        );
    }

    /**
     * Retry failed job
     */
    public function retry(int $id)
    {
        $this->queue->retryFailedJob($id);

        return ApiResponse::success(
            data: [],
            code: 'QUEUE_FAILED_RETRY_SUCCESS',
            message: 'Đã đưa failed job vào queue để chạy lại',
        );
    }

    /**
     * Xoá failed job khỏi failed_jobs
     */
    public function forget(int $id)
    {
        $this->queue->forgetFailedJob($id);

        return ApiResponse::success(
            data: [],
            code: 'QUEUE_FAILED_FORGET_SUCCESS',
            message: 'Đã xoá failed job',
        );
    }
}

