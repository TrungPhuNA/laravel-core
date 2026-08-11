<?php

namespace Modules\Monitor\Application\Support;

use Illuminate\Support\Carbon;

/**
 * Kết quả tra cứu hạn domain.
 */
final class DomainCheckResult
{
    public function __construct(
        public readonly ?Carbon $expiresAt,
        public readonly ?string $registrar,
        /** @var list<string> */
        public readonly array $nameservers,
        public readonly string $method,
        public readonly ?string $error,
        public readonly string $raw,
    ) {}

    public function isOk(): bool
    {
        return $this->error === null;
    }
}