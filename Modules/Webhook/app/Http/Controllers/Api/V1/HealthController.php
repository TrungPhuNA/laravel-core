<?php

namespace Modules\Webhook\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;

/**
 * @group Webhook
 * @subgroup Hệ thống
 */
final class HealthController extends Controller
{
    /**
     * Health check
     *
     * API dùng để kiểm tra module đã được load route và hoạt động.
     *
     * @unauthenticated
     */
    public function show()
    {
        return ApiResponse::success(
            data: [
                'module' => 'Webhook',
                'time' => now()->toISOString(),
            ],
            code: 'WEBHOOK_HEALTH_OK',
            message: 'Module hoạt động',
        );
    }
}
