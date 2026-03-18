<?php

namespace Modules\Setting\Http\Controllers\Api\V1\Queue;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\QueueServiceInterface;
use Modules\Setting\Http\Requests\Api\V1\Queue\QueueJobIndexRequest;
use Modules\Setting\Http\Resources\Api\V1\Queue\QueueJobResource;

/**
 * @group Cài đặt
 * @subgroup Hàng đợi
 */
final class QueueJobController extends Controller
{
    public function __construct(
        private readonly QueueServiceInterface $queue,
    ) {}

    /**
     * Danh sách job (jobs)
     */
    public function index(QueueJobIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $paginator = $this->queue->paginateJobs($params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: QueueJobResource::collection($paginator->items()),
            code: 'QUEUE_JOBS_LIST_SUCCESS',
            message: 'Lấy danh sách job thành công',
        );
    }

    /**
     * Chi tiết job
     */
    public function show(int $id)
    {
        $job = $this->queue->getJobById($id);

        return ApiResponse::success(
            data: [
                'job' => new QueueJobResource($job),
                'payload' => $job->payload, // full payload de debug
            ],
            code: 'QUEUE_JOB_SHOW_SUCCESS',
            message: 'Lấy chi tiết job thành công',
        );
    }
}

