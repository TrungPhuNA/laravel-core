<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Application\Contracts\ProductServiceInterface;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\ProductIndexRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\ProductStoreRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\ProductUpdateRequest;
use Modules\Ecommerce\Http\Resources\Api\V1\ProductResource;

/**
 * @group Ecommerce
 * @subgroup Admin - Products
 */
final class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $products,
    ) {}

    public function index(ProductIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $p = $this->products->paginate($params);

        return ApiResponse::paginated(
            paginator: $p,
            items: ProductResource::collection($p->items()),
            code: 'ECM_PRODUCT_LIST_SUCCESS',
            message: 'Lấy danh sách sản phẩm thành công',
        );
    }

    public function show(int $id)
    {
        $product = $this->products->getById($id);

        return ApiResponse::success(
            data: ['product' => new ProductResource($product)],
            code: 'ECM_PRODUCT_SHOW_SUCCESS',
            message: 'Lấy sản phẩm thành công',
        );
    }

    public function store(ProductStoreRequest $request)
    {
        $product = $this->products->create($request->validated());

        return ApiResponse::success(
            data: ['product' => new ProductResource($product)],
            code: 'ECM_PRODUCT_CREATE_SUCCESS',
            message: 'Tạo sản phẩm thành công',
            status: 201,
        );
    }

    public function update(int $id, ProductUpdateRequest $request)
    {
        $product = $this->products->update($id, $request->validated());

        return ApiResponse::success(
            data: ['product' => new ProductResource($product)],
            code: 'ECM_PRODUCT_UPDATE_SUCCESS',
            message: 'Cập nhật sản phẩm thành công',
        );
    }

    public function destroy(int $id)
    {
        $this->products->delete($id);

        return ApiResponse::success(
            data: [],
            code: 'ECM_PRODUCT_DELETE_SUCCESS',
            message: 'Xoá sản phẩm thành công',
        );
    }
}

