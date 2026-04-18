<?php

namespace Modules\CheatSheet\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\CheatSheet\Application\Contracts\CheatSheetPublicServiceInterface;
use Modules\CheatSheet\Http\Requests\Api\V1\PublicCheatSheetIndexRequest;
use Modules\CheatSheet\Http\Resources\Api\V1\PublicCheatSheetListItemResource;
use Modules\CheatSheet\Http\Resources\Api\V1\PublicCheatSheetResource;

/**
 * @group Cheat sheets (Public)
 *
 * Endpoint public để browse cheat sheets public.
 */
final class PublicCheatSheetController extends Controller
{
    public function __construct(
        private readonly CheatSheetPublicServiceInterface $public,
    ) {}

    public function index(PublicCheatSheetIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $paginator = $this->public->paginate($params);

        // load author to show name
        $paginator->getCollection()->loadMissing(['tags', 'author']);

        return ApiResponse::paginated(
            paginator: $paginator,
            items: PublicCheatSheetListItemResource::collection($paginator->items()),
            code: 'CHEATSHEET_PUBLIC_LIST_SUCCESS',
            message: 'Lấy danh sách cheat sheets public thành công',
        );
    }

    public function show(int $id)
    {
        $sheet = $this->public->getById($id)->loadMissing(['tags', 'author']);

        return ApiResponse::success(
            data: ['cheat_sheet' => new PublicCheatSheetResource($sheet)],
            code: 'CHEATSHEET_PUBLIC_SHOW_SUCCESS',
            message: 'Lấy cheat sheet public thành công',
        );
    }

    public function topics()
    {
        $q = request()->query('q');
        $limit = (int) request()->query('limit', 60);

        $topics = $this->public->listTopics(is_string($q) ? $q : null, $limit);

        return ApiResponse::success(
            data: ['topics' => $topics],
            code: 'CHEATSHEET_PUBLIC_TOPICS_SUCCESS',
            message: 'Lấy danh sách chủ đề public thành công',
        );
    }
}

