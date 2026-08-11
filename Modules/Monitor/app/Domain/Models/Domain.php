<?php

namespace Modules\Monitor\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $domain
 * @property string|null $note
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string|null $registrar
 * @property array<int, string>|null $nameservers
 * @property string $check_status
 * @property \Illuminate\Support\Carbon|null $last_check_at
 * @property string|null $last_check_error
 */
final class Domain extends Model
{
    protected $table = 'dmn_domains';

    protected $fillable = [
        'domain',
        'note',
        'is_active',
        'expires_at',
        'registrar',
        'nameservers',
        'check_status',
        'last_check_at',
        'last_check_error',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'nameservers' => 'array',
        'last_check_at' => 'datetime',
    ];

    public function checkLogs(): HasMany
    {
        return $this->hasMany(DomainCheckLog::class, 'domain_id');
    }

    /**
     * Số ngày còn lại đến hết hạn. null nếu chưa có expires_at.
     */
    public function daysRemaining(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        // diffInDays($other, false) trả về $other - $this; nên gọi theo chiều expires - now.
        return (int) ceil(now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false));
    }

    /**
     * Phân loại trạng thái hiển thị theo ngưỡng trong config monitor.warning.
     * Trả về một trong: ok|soon|critical|expired|unknown|error
     */
    public function badge(): string
    {
        if ($this->check_status === 'error') {
            return 'error';
        }

        $days = $this->daysRemaining();
        if ($days === null) {
            return 'unknown';
        }

        $critical = (int) config('monitor.warning.critical_days', 7);
        $soon = (int) config('monitor.warning.soon_days', 30);

        if ($days < 0) {
            return 'expired';
        }
        if ($days <= $critical) {
            return 'critical';
        }
        if ($days <= $soon) {
            return 'soon';
        }

        return 'ok';
    }
}