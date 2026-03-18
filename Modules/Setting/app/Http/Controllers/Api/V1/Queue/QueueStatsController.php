<?php

namespace Modules\Setting\Http\Controllers\Api\V1\Queue;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\QueueServiceInterface;

/**
 * @group Cài đặt
 * @subgroup Hàng đợi
 */
final class QueueStatsController extends Controller
{
    public function __construct(
        private readonly QueueServiceInterface $queue,
    ) {}

    /**
     * Thống kê queue
     */
    public function show()
    {
        return ApiResponse::success(
            data: $this->queue->stats(),
            code: 'QUEUE_STATS_SUCCESS',
            message: 'Lấy thống kê queue thành công',
        );
    }
}

