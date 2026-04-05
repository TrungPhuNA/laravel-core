<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Application\Contracts\OrderServiceInterface;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\OrderIndexRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\OrderStoreRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\OrderUpdateRequest;
use Modules\Ecommerce\Http\Resources\Api\V1\OrderResource;

/**
 * @group Ecommerce
 * @subgroup Admin - Orders
 */
final class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface $orders,
    ) {}

    public function index(OrderIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $p = $this->orders->paginate($params);

        return ApiResponse::paginated(
            paginator: $p,
            items: OrderResource::collection($p->items()),
            code: 'ECM_ORDER_LIST_SUCCESS',
            message: 'Lấy danh sách đơn hàng thành công',
        );
    }

    public function show(int $id)
    {
        $order = $this->orders->getById($id);

        return ApiResponse::success(
            data: ['order' => new OrderResource($order)],
            code: 'ECM_ORDER_SHOW_SUCCESS',
            message: 'Lấy đơn hàng thành công',
        );
    }

    public function store(OrderStoreRequest $request)
    {
        $order = $this->orders->create($request->validated());

        return ApiResponse::success(
            data: ['order' => new OrderResource($order)],
            code: 'ECM_ORDER_CREATE_SUCCESS',
            message: 'Tạo đơn hàng thành công',
            status: 201,
        );
    }

    public function update(int $id, OrderUpdateRequest $request)
    {
        $order = $this->orders->update($id, $request->validated());

        return ApiResponse::success(
            data: ['order' => new OrderResource($order)],
            code: 'ECM_ORDER_UPDATE_SUCCESS',
            message: 'Cập nhật đơn hàng thành công',
        );
    }

    public function destroy(int $id)
    {
        $this->orders->delete($id);

        return ApiResponse::success(
            data: [],
            code: 'ECM_ORDER_DELETE_SUCCESS',
            message: 'Xoá đơn hàng thành công',
        );
    }
}

