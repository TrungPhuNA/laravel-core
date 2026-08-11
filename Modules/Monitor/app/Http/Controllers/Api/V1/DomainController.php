<?php

namespace Modules\Monitor\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Monitor\Application\Contracts\DomainMonitorServiceInterface;
use Modules\Monitor\Domain\Models\Domain;
use Modules\Monitor\Http\Requests\Api\V1\DomainBulkStoreRequest;
use Modules\Monitor\Http\Requests\Api\V1\DomainIndexRequest;
use Modules\Monitor\Http\Requests\Api\V1\DomainStoreRequest;
use Modules\Monitor\Http\Requests\Api\V1\DomainUpdateRequest;
use Modules\Monitor\Http\Resources\Api\V1\DomainResource;

/**
 * @group Monitor
 * @subgroup Domains
 */
final class DomainController extends Controller
{
    public function __construct(
        private readonly DomainMonitorServiceInterface $monitor,
    ) {}

    /**
     * Danh sách domain theo dõi
     */
    public function index(DomainIndexRequest $request)
    {
        $paginator = $this->monitor->paginate($request->apiQueryParams());

        return ApiResponse::paginated(
            paginator: $paginator,
            items: DomainResource::collection($paginator->items()),
            code: 'MONITOR_DOMAIN_LIST_SUCCESS',
            message: 'Lấy danh sách domain thành công',
        );
    }

    /**
     * Thêm 1 domain
     */
    public function store(DomainStoreRequest $request)
    {
        $result = $this->monitor->importDomains([$request->validated('domain')]);

        if ($result['imported'] === 0) {
            return ApiResponse::fail(
                data: ['domain' => 'Domain không hợp lệ hoặc đã tồn tại'],
                code: 'MONITOR_DOMAIN_INVALID',
                message: 'Không thêm được domain',
                status: 422,
            );
        }

        $domain = Domain::query()->where('domain', $result['created'][0])->firstOrFail();

        return ApiResponse::success(
            data: ['domain' => new DomainResource($domain)],
            code: 'MONITOR_DOMAIN_CREATE_SUCCESS',
            message: 'Thêm domain thành công',
            status: 201,
        );
    }

    /**
     * Thêm nhiều domain (paste danh sách)
     */
    public function storeBulk(DomainBulkStoreRequest $request)
    {
        $result = $this->monitor->importDomains($request->validated('domains'));

        return ApiResponse::success(
            data: $result,
            code: 'MONITOR_DOMAIN_BULK_SUCCESS',
            message: "Đã thêm {$result['imported']} domain" . ($result['skipped'] > 0 ? ", bỏ qua {$result['skipped']}" : ''),
            status: 201,
        );
    }

    /**
     * Chi tiết domain
     */
    public function show(int $id)
    {
        $domain = Domain::query()->findOrFail($id);

        return ApiResponse::success(
            data: ['domain' => new DomainResource($domain)],
            code: 'MONITOR_DOMAIN_SHOW_SUCCESS',
            message: 'Lấy domain thành công',
        );
    }

    /**
     * Cập nhật domain
     */
    public function update(int $id, DomainUpdateRequest $request)
    {
        $domain = $this->monitor->update($id, $request->validated());

        return ApiResponse::success(
            data: ['domain' => new DomainResource($domain)],
            code: 'MONITOR_DOMAIN_UPDATE_SUCCESS',
            message: 'Cập nhật domain thành công',
        );
    }

    /**
     * Xoá domain
     */
    public function destroy(int $id)
    {
        $this->monitor->delete($id);

        return ApiResponse::success(
            data: null,
            code: 'MONITOR_DOMAIN_DELETE_SUCCESS',
            message: 'Xoá domain thành công',
        );
    }
}