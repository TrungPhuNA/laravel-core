<?php

namespace Modules\CheatSheet\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Routing\Controller;
use Modules\CheatSheet\Application\Contracts\CheatSheetServiceInterface;
use Modules\CheatSheet\Http\Requests\Api\V1\CheatSheetIndexRequest;
use Modules\CheatSheet\Http\Requests\Api\V1\CheatSheetStoreRequest;
use Modules\CheatSheet\Http\Requests\Api\V1\CheatSheetUpdateRequest;
use Modules\CheatSheet\Http\Resources\Api\V1\CheatSheetResource;
use Modules\CheatSheet\Http\Resources\Api\V1\CheatSheetTagResource;
use Modules\CheatSheet\Http\Resources\Api\V1\CheatSheetTopicResource;

/**
 * @group Cheat sheets
 *
 * Cheat sheet cá nhân (có thể public sau). Hiện tại tất cả endpoint yêu cầu đăng nhập (Sanctum).
 */
final class CheatSheetController extends Controller
{
    public function __construct(
        private readonly CheatSheetServiceInterface $sheets,
    ) {}

    /**
     * Danh sách cheat sheets
     *
     * Hỗ trợ query:
     * - filters[q] (search title/body)
     * - filters[tag] (lọc theo tag, có thể CSV)
     * - filters[visibility] (private|unlisted|public)
     * - sort=id,title,created_at,updated_at,published_at (có thể thêm "-" để desc)
     * - page, per_page
     */
    public function index(CheatSheetIndexRequest $request)
    {
        $user = $this->user();
        $params = $request->apiQueryParams();
        $paginator = $this->sheets->paginateForUser($user, $params);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: CheatSheetResource::collection($paginator->items()),
            code: 'CHEATSHEET_LIST_SUCCESS',
            message: 'Lấy danh sách cheat sheet thành công',
        );
    }

    /**
     * Tạo cheat sheet
     */
    public function store(CheatSheetStoreRequest $request)
    {
        $user = $this->user();
        $sheet = $this->sheets->createForUser($user, $request->validated());

        return ApiResponse::success(
            data: ['cheat_sheet' => new CheatSheetResource($sheet)],
            code: 'CHEATSHEET_CREATE_SUCCESS',
            message: 'Tạo cheat sheet thành công',
            status: 201,
        );
    }

    /**
     * Chi tiết cheat sheet
     */
    public function show(int $id)
    {
        $user = $this->user();
        $sheet = $this->sheets->getForUserById($user, $id);

        return ApiResponse::success(
            data: ['cheat_sheet' => new CheatSheetResource($sheet)],
            code: 'CHEATSHEET_SHOW_SUCCESS',
            message: 'Lấy cheat sheet thành công',
        );
    }

    /**
     * Cập nhật cheat sheet
     */
    public function update(int $id, CheatSheetUpdateRequest $request)
    {
        $user = $this->user();
        $sheet = $this->sheets->updateForUser($user, $id, $request->validated());

        return ApiResponse::success(
            data: ['cheat_sheet' => new CheatSheetResource($sheet)],
            code: 'CHEATSHEET_UPDATE_SUCCESS',
            message: 'Cập nhật cheat sheet thành công',
        );
    }

    /**
     * Xoá cheat sheet (soft delete)
     */
    public function destroy(int $id)
    {
        $user = $this->user();
        $this->sheets->deleteForUser($user, $id);

        return ApiResponse::success(
            data: [],
            code: 'CHEATSHEET_DELETE_SUCCESS',
            message: 'Xoá cheat sheet thành công',
        );
    }

    /**
     * Gợi ý tags
     *
     * Query:
     * - q (search theo name)
     * - limit (1..50, default 20)
     */
    public function tags()
    {
        $user = $this->user();
        $q = request()->query('q');
        $limit = (int) request()->query('limit', 20);

        $tags = $this->sheets->listTagsForUser($user, is_string($q) ? $q : null, $limit);

        return ApiResponse::success(
            data: ['tags' => CheatSheetTagResource::collection($tags)],
            code: 'CHEATSHEET_TAGS_SUCCESS',
            message: 'Lấy danh sách tags thành công',
        );
    }

    /**
     * Danh sách chủ đề (topics)
     *
     * Hiển thị tags kèm count cheat sheets theo từng tag.
     *
     * Query:
     * - q (optional)
     * - limit (default 50, max 100)
     */
    public function topics()
    {
        $user = $this->user();
        $q = request()->query('q');
        $limit = (int) request()->query('limit', 50);

        $topics = $this->sheets->listTopicsForUser($user, is_string($q) ? $q : null, $limit);

        return ApiResponse::success(
            data: ['topics' => CheatSheetTopicResource::collection($topics)],
            code: 'CHEATSHEET_TOPICS_SUCCESS',
            message: 'Lấy danh sách chủ đề thành công',
        );
    }

    private function user(): User
    {
        /** @var User $user */
        $user = request()->user();

        return $user;
    }
}
