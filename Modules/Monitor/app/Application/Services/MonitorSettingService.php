<?php

namespace Modules\Monitor\Application\Services;

use Illuminate\Support\Facades\Schema;
use Modules\Monitor\Application\Contracts\MonitorSettingServiceInterface;
use Modules\Monitor\Domain\Models\MonitorSetting;

final class MonitorSettingService implements MonitorSettingServiceInterface
{
    /**
     * Các key cấu hình cho phép chỉnh qua UI (flat, dot-notation).
     */
    private const SETTING_KEYS = [
        'check.rdap.enabled',
        'check.whois.enabled',
        'check.third_party.enabled',
        'check.third_party.api_key',
        'warning.normal_days',
        'warning.soon_days',
        'warning.critical_days',
    ];

    public function all(): array
    {
        $overrides = $this->dbValues();

        return [
            'check' => [
                'rdap' => [
                    'enabled' => $overrides['check.rdap.enabled'] ?? (bool) config('monitor.check.rdap.enabled', true),
                ],
                'whois' => [
                    'enabled' => $overrides['check.whois.enabled'] ?? (bool) config('monitor.check.whois.enabled', true),
                ],
                'third_party' => [
                    'enabled' => $overrides['check.third_party.enabled'] ?? (bool) config('monitor.check.third_party.enabled', false),
                    'api_key' => $overrides['check.third_party.api_key'] ?? (string) config('monitor.check.third_party.api_key', ''),
                ],
            ],
            'warning' => [
                'normal_days' => (int) ($overrides['warning.normal_days'] ?? config('monitor.warning.normal_days', 60)),
                'soon_days' => (int) ($overrides['warning.soon_days'] ?? config('monitor.warning.soon_days', 30)),
                'critical_days' => (int) ($overrides['warning.critical_days'] ?? config('monitor.warning.critical_days', 7)),
            ],
        ];
    }

    public function update(array $data): void
    {
        $overrides = $this->dbValues();

        foreach (self::SETTING_KEYS as $key) {
            $value = data_get($data, $key);
            if ($value === null) {
                continue;
            }

            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif (is_int($value) || is_float($value)) {
                $value = (string) $value;
            }

            $overrides[$key] = $value;
            MonitorSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public function loadIntoConfig(): void
    {
        foreach ($this->dbValues() as $key => $value) {
            if (!in_array($key, self::SETTING_KEYS, true)) {
                continue;
            }

            config()->set('monitor.' . $key, $this->castValue($key, $value));
        }
    }

    /**
     * @return array<string, string>
     */
    private function dbValues(): array
    {
        if (!Schema::hasTable('dmn_settings')) {
            return [];
        }

        return MonitorSetting::query()
            ->whereIn('key', self::SETTING_KEYS)
            ->pluck('value', 'key')
            ->map(fn ($v) => (string) $v)
            ->all();
    }

    private function castValue(string $key, mixed $value): mixed
    {
        if (str_ends_with($key, 'days')) {
            return (int) $value;
        }

        if (str_ends_with($key, 'enabled')) {
            return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
        }

        return (string) $value;
    }
}