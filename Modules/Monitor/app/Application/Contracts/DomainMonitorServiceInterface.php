<?php

namespace Modules\Monitor\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Monitor\Domain\Models\Domain;

interface DomainMonitorServiceInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    /**
     * Import nhiều domain (chuẩn hoá, dedupe, bỏ qua invalid).
     *
     * @param list<string> $domains
     * @return array{imported: int, skipped: int, created: list<string>}
     */
    public function importDomains(array $domains): array;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): Domain;

    public function delete(int $id): void;

    /**
     * Check 1 domain ngay lập tức, cập nhật dữ liệu + tạo log.
     */
    public function checkNow(int $id): Domain;

    /**
     * Check tất cả domain đang active.
     */
    public function checkAll(): int;

    /**
     * @return Collection<int, \Modules\Monitor\Domain\Models\DomainCheckLog>
     */
    public function getCheckLogs(int $id, int $limit = 20): Collection;
}