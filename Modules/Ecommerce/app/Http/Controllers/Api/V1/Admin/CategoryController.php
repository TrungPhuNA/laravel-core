<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Application\Contracts\CategoryServiceInterface;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\CategoryIndexRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\CategoryStoreRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\CategoryUpdateRequest;
use Modules\Ecommerce\Http\Resources\Api\V1\CategoryResource;

/**
 * @group Ecommerce
 * @subgroup Admin - Categories
 */
final class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryServiceInterface $categories,
    ) {}

    public function index(CategoryIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $p = $this->categories->paginate($params);

        return ApiResponse::paginated(
            paginator: $p,
            items: CategoryResource::collection($p->items()),
            code: 'ECM_CATEGORY_LIST_SUCCESS',
            message: 'Lấy danh sách danh mục thành công',
        );
    }

    public function show(int $id)
    {
        $category = $this->categories->getById($id);

        return ApiResponse::success(
            data: ['category' => new CategoryResource($category)],
            code: 'ECM_CATEGORY_SHOW_SUCCESS',
            message: 'Lấy danh mục thành công',
        );
    }

    public function store(CategoryStoreRequest $request)
    {
        $category = $this->categories->create($request->validated());

        return ApiResponse::success(
            data: ['category' => new CategoryResource($category)],
            code: 'ECM_CATEGORY_CREATE_SUCCESS',
            message: 'Tạo danh mục thành công',
            status: 201,
        );
    }

    public function update(int $id, CategoryUpdateRequest $request)
    {
        $category = $this->categories->update($id, $request->validated());

        return ApiResponse::success(
            data: ['category' => new CategoryResource($category)],
            code: 'ECM_CATEGORY_UPDATE_SUCCESS',
            message: 'Cập nhật danh mục thành công',
        );
    }

    public function destroy(int $id)
    {
        $this->categories->delete($id);

        return ApiResponse::success(
            data: [],
            code: 'ECM_CATEGORY_DELETE_SUCCESS',
            message: 'Xoá danh mục thành công',
        );
    }
}

