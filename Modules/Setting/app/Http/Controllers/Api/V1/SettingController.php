<?php

namespace Modules\Setting\Http\Controllers\Api\V1;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\UserType;
use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
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
     * Lấy setting theo key
     *
     * Nếu setting là public: không cần đăng nhập.
     * Nếu setting không public: yêu cầu user_type là ADMIN hoặc SYSTEM.
     *
     * @subgroup Theo key
     */
    public function show(string $key, Request $request)
    {
        $public = $this->settings->getPublicByKey($key);

        if ($public) {
            return ApiResponse::success(
                data: ['item' => new SettingResource($public)],
                code: 'SETTING_GET_SUCCESS',
                message: 'Lấy setting thành công',
            );
        }

        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if (!$user) {
            throw new ApiException(
                errorCode: ErrorCode::UNAUTHORIZED->value,
                message: __('messages.unauthorized'),
                status: 401,
            );
        }

        $userType = $user->user_type;
        $userTypeValue = $userType instanceof UserType ? $userType->value : (string) $userType;

        if (!in_array($userTypeValue, [UserType::ADMIN->value, UserType::SYSTEM->value], true)) {
            throw new ApiException(
                errorCode: ErrorCode::FORBIDDEN->value,
                message: __('messages.forbidden'),
                status: 403,
            );
        }

        $item = $this->settings->getByKey($key);

        if (!$item) {
            throw new ApiException(
                errorCode: ErrorCode::NOT_FOUND->value,
                message: __('messages.not_found'),
                status: 404,
            );
        }

        return ApiResponse::success(
            data: ['item' => new SettingResource($item)],
            code: 'SETTING_GET_SUCCESS',
            message: 'Lấy setting thành công',
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
