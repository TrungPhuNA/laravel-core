<?php

namespace Modules\Monitor\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Monitor\Application\Contracts\MonitorSettingServiceInterface;
use Modules\Monitor\Http\Requests\Api\V1\SettingsUpdateRequest;

/**
 * @group Monitor
 * @subgroup Cấu hình
 */
final class MonitorSettingController extends Controller
{
    public function __construct(
        private readonly MonitorSettingServiceInterface $settings,
    ) {}

    /**
     * Lấy cấu hình monitor hiện tại
     */
    public function show()
    {
        return ApiResponse::success(
            data: ['settings' => $this->settings->all()],
            code: 'MONITOR_SETTING_SHOW_SUCCESS',
            message: 'Lấy cấu hình thành công',
        );
    }

    /**
     * Cập nhật cấu hình monitor
     */
    public function update(SettingsUpdateRequest $request)
    {
        $this->settings->update($request->validated());
        $this->settings->loadIntoConfig();

        return ApiResponse::success(
            data: ['settings' => $this->settings->all()],
            code: 'MONITOR_SETTING_UPDATE_SUCCESS',
            message: 'Lưu cấu hình thành công',
        );
    }
}