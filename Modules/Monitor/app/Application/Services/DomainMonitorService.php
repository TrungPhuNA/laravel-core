<?php

namespace Modules\Monitor\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Monitor\Application\Contracts\DomainMonitorServiceInterface;
use Modules\Monitor\Application\Contracts\DomainWhoisServiceInterface;
use Modules\Monitor\Domain\Models\Domain;
use Modules\Monitor\Infrastructure\Contracts\DomainCheckLogRepositoryInterface;
use Modules\Monitor\Infrastructure\Contracts\DomainRepositoryInterface;

final class DomainMonitorService implements DomainMonitorServiceInterface
{
    public function __construct(
        private readonly DomainRepositoryInterface $domains,
        private readonly DomainCheckLogRepositoryInterface $checkLogs,
        private readonly DomainWhoisServiceInterface $whois,
    ) {}

    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->domains->paginate($params);
    }

    public function importDomains(array $domains): array
    {
        $created = [];
        $skipped = 0;

        foreach ($domains as $value) {
            $normalized = $this->normalizeDomain(is_string($value) ? $value : '');
            if ($normalized === null) {
                $skipped++;
                continue;
            }

            if ($this->domains->findByDomain($normalized) !== null) {
                $skipped++;
                continue;
            }

            $this->domains->create(['domain' => $normalized, 'check_status' => 'unknown']);
            $created[] = $normalized;
        }

        return [
            'imported' => count($created),
            'skipped' => $skipped,
            'created' => $created,
        ];
    }

    public function update(int $id, array $data): Domain
    {
        $domain = $this->domains->findOrFail($id);
        $data = array_intersect_key($data, array_flip(['note', 'is_active']));

        return $this->domains->update($domain, $data);
    }

    public function delete(int $id): void
    {
        $this->domains->delete($this->domains->findOrFail($id));
    }

    public function checkNow(int $id): Domain
    {
        $domain = $this->domains->findOrFail($id);
        $result = $this->whois->check($domain->domain);

        $now = now();

        if ($result->isOk()) {
            $this->domains->update($domain, [
                'expires_at' => $result->expiresAt,
                'registrar' => $result->registrar,
                'nameservers' => $result->nameservers,
                'check_status' => 'ok',
                'last_check_at' => $now,
                'last_check_error' => null,
            ]);
        } else {
            $this->domains->update($domain, [
                'check_status' => 'error',
                'last_check_at' => $now,
                'last_check_error' => $result->error,
            ]);
        }

        $this->checkLogs->create([
            'domain_id' => $domain->id,
            'status' => $result->isOk() ? 'ok' : 'error',
            'expires_at_found' => $result->expiresAt,
            'registrar' => $result->registrar,
            'method' => $result->method,
            'error_message' => $result->error,
            'raw_response' => $result->raw,
            'checked_at' => $now,
        ]);

        return $this->domains->findOrFail($id);
    }

    public function checkAll(): int
    {
        $count = 0;
        foreach ($this->domains->listActive() as $domain) {
            try {
                $this->checkNow((int) $domain->id);
                $count++;
            } catch (\Throwable $e) {
                Log::warning('=== MONITOR CHECK DOMAIN FAILED ===', [
                    'domain_id' => $domain->id,
                    'domain' => $domain->domain,
                    'error' => $e->getMessage(),
                ]);
            }

            // Tránh bị rate-limit/admin chặn khi check nhiều domain liên tiếp.
            usleep(200_000);
        }

        return $count;
    }

    public function getCheckLogs(int $id, int $limit = 20): Collection
    {
        return $this->checkLogs->listForDomain($id, $limit);
    }

    private function normalizeDomain(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Bỏ protocol (http://, https://).
        if (preg_match('#^[a-zA-Z][a-zA-Z0-9+.\-]*://#', $value)) {
            $value = (string) preg_replace('#^[a-zA-Z][a-zA-Z0-9+.\-]*://#', '', $value);
        }

        // Bỏ path/query nếu dán kèm URL đầy đủ.
        $value = (string) preg_replace('~[/?#].*$~', '', $value);

        // Bỏ port.
        if (str_contains($value, ':')) {
            $value = explode(':', $value, 2)[0];
        }

        $value = strtolower(trim($value, ". \t\n\r\0\x0B"));
        if (str_contains($value, ' ')) {
            return null;
        }

        if (!preg_match('/^[a-z0-9\-]+(\.[a-z0-9\-]+)+$/', $value)) {
            return null;
        }

        return $value;
    }
}