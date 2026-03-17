<?php

namespace Modules\Setting\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Setting\Application\Contracts\SettingServiceInterface;
use Modules\Setting\Http\Requests\Api\V1\UpsertSettingsRequest;
use Modules\Setting\Http\Resources\Api\V1\SettingResource;

/**
 * @group Cài đặt
 */
final class SettingController extends Controller
{
    public function __construct(
        private readonly SettingServiceInterface $settings,
    ) {}

    /**
     * Danh sách setting công khai
     *
     * @subgroup Công khai
     * @unauthenticated
     */
    public function public()
    {
        $items = $this->settings->listPublic();

        return ApiResponse::success(
            data: ['items' => SettingResource::collection($items)],
            code: 'SETTING_PUBLIC_LIST_SUCCESS',
            message: 'Lấy setting công khai thành công',
        );
    }

    /**
     * Danh sách tất cả setting
     *
     * @subgroup Quản trị
     * @authenticated
     */
    public function index()
    {
        $items = $this->settings->listAll();

        return ApiResponse::success(
            data: ['items' => SettingResource::collection($items)],
            code: 'SETTING_LIST_SUCCESS',
            message: 'Lấy danh sách setting thành công',
        );
    }

    /**
     * Upsert settings (bulk)
     *
     * @subgroup Quản trị
     * @authenticated
     */
    public function upsert(UpsertSettingsRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $this->settings->upsert($request->validated('items'), (int) $user->id);

        return ApiResponse::success(
            data: null,
            code: 'SETTING_UPSERT_SUCCESS',
            message: 'Cập nhật setting thành công',
        );
    }
}

