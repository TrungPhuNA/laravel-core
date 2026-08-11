<?php

namespace Modules\Monitor\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Monitor\Application\Contracts\DomainMonitorServiceInterface;
use Modules\Monitor\Http\Resources\Api\V1\DomainCheckLogResource;
use Modules\Monitor\Http\Resources\Api\V1\DomainResource;

/**
 * @group Monitor
 * @subgroup Check
 */
final class DomainCheckController extends Controller
{
    public function __construct(
        private readonly DomainMonitorServiceInterface $monitor,
    ) {}

    /**
     * Check hạn domain ngay lập tức
     */
    public function check(int $id, Request $request)
    {
        $domain = $this->monitor->checkNow($id);

        return ApiResponse::success(
            data: ['domain' => new DomainResource($domain)],
            code: 'MONITOR_DOMAIN_CHECK_SUCCESS',
            message: 'Đã check domain',
        );
    }

    /**
     * Lịch sử check của domain
     */
    public function logs(int $id, Request $request)
    {
        $limit = (int) $request->query('limit', 20);
        $limit = max(1, min(100, $limit));

        $logs = $this->monitor->getCheckLogs($id, $limit);

        return ApiResponse::success(
            data: ['logs' => DomainCheckLogResource::collection($logs)],
            code: 'MONITOR_DOMAIN_LOG_LIST_SUCCESS',
            message: 'Lấy lịch sử check thành công',
        );
    }
}