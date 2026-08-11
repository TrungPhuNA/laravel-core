<?php

namespace Modules\Monitor\Application\Contracts;

use Modules\Monitor\Application\Support\DomainCheckResult;

interface DomainWhoisServiceInterface
{
    /**
     * Tra cứu thông tin domain theo thứ tự: rdap -> whois -> third_party.
     */
    public function check(string $domain): DomainCheckResult;
}