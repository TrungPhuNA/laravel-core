<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Application\Contracts\CustomerServiceInterface;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\CustomerIndexRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\CustomerStoreRequest;
use Modules\Ecommerce\Http\Requests\Api\V1\Admin\CustomerUpdateRequest;
use Modules\Ecommerce\Http\Resources\Api\V1\CustomerResource;

/**
 * @group Ecommerce
 * @subgroup Admin - Customers
 */
final class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerServiceInterface $customers,
    ) {}

    public function index(CustomerIndexRequest $request)
    {
        $params = $request->apiQueryParams();
        $p = $this->customers->paginate($params);

        return ApiResponse::paginated(
            paginator: $p,
            items: CustomerResource::collection($p->items()),
            code: 'ECM_CUSTOMER_LIST_SUCCESS',
            message: 'Lấy danh sách khách hàng thành công',
        );
    }

    public function show(int $id)
    {
        $customer = $this->customers->getById($id);

        return ApiResponse::success(
            data: ['customer' => new CustomerResource($customer)],
            code: 'ECM_CUSTOMER_SHOW_SUCCESS',
            message: 'Lấy khách hàng thành công',
        );
    }

    public function store(CustomerStoreRequest $request)
    {
        $customer = $this->customers->create($request->validated());

        return ApiResponse::success(
            data: ['customer' => new CustomerResource($customer)],
            code: 'ECM_CUSTOMER_CREATE_SUCCESS',
            message: 'Tạo khách hàng thành công',
            status: 201,
        );
    }

    public function update(int $id, CustomerUpdateRequest $request)
    {
        $customer = $this->customers->update($id, $request->validated());

        return ApiResponse::success(
            data: ['customer' => new CustomerResource($customer)],
            code: 'ECM_CUSTOMER_UPDATE_SUCCESS',
            message: 'Cập nhật khách hàng thành công',
        );
    }

    public function destroy(int $id)
    {
        $this->customers->delete($id);

        return ApiResponse::success(
            data: [],
            code: 'ECM_CUSTOMER_DELETE_SUCCESS',
            message: 'Xoá khách hàng thành công',
        );
    }
}

