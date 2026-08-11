<?php

namespace Modules\Monitor\Console;

use Illuminate\Console\Command;
use Modules\Monitor\Application\Contracts\DomainMonitorServiceInterface;

class CheckDomainsCommand extends Command
{
    protected $signature = 'monitor:domains:check';

    protected $description = 'Check hạn tất cả domain đang active và cập nhật dữ liệu';

    public function handle(DomainMonitorServiceInterface $monitor): int
    {
        $count = $monitor->checkAll();
        $this->info("Đã check {$count} domain.");

        return self::SUCCESS;
    }
}