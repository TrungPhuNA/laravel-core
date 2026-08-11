<?php

namespace Modules\Monitor\Application\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Modules\Monitor\Application\Contracts\DomainWhoisServiceInterface;
use Modules\Monitor\Application\Support\DomainCheckResult;

/**
 * Tra cứu hạn domain thông qua nguồn công khai (không cần truy cập server của domain).
 *
 * Thứ tự thử: RDAP (HTTP) -> WHOIS (port 43) -> Third-party API (nếu bật).
 */
final class DomainWhoisService implements DomainWhoisServiceInterface
{
    public function check(string $domain): DomainCheckResult
    {
        $domain = strtolower(trim($domain));

        $lastError = null;
        $lastMethod = 'none';

        if (config('monitor.check.rdap.enabled', true)) {
            $result = $this->checkRdap($domain);
            if ($result->isOk()) {
                return $result;
            }
            $lastError = $result->error;
            $lastMethod = 'rdap';
        }

        if (config('monitor.check.whois.enabled', true)) {
            $result = $this->checkWhois($domain);
            if ($result->isOk()) {
                return $result;
            }
            $lastError = $result->error;
            $lastMethod = 'whois';
        }

        if (config('monitor.check.third_party.enabled', false)) {
            $result = $this->checkThirdParty($domain);
            if ($result->isOk()) {
                return $result;
            }
            $lastError = $result->error;
            $lastMethod = 'third_party';
        }

        return new DomainCheckResult(
            expiresAt: null,
            registrar: null,
            nameservers: [],
            method: $lastMethod,
            error: $lastError ?? 'Không có phương thức tra cứu nào được bật',
            raw: '',
        );
    }

    private function checkRdap(string $domain): DomainCheckResult
    {
        $endpoint = (string) config('monitor.check.rdap.endpoint', 'https://rdap.org/domain/{domain}');
        $endpoint = str_replace('{domain}', $domain, $endpoint);
        $timeout = (int) config('monitor.check.rdap.timeout', 15);

        try {
            $res = Http::timeout($timeout)
                ->withHeaders(['Accept' => 'application/rdap+json'])
                ->get($endpoint);

            if (!$res->successful()) {
                return $this->failed('rdap', 'RDAP trả về HTTP ' . $res->status(), $res->body());
            }

            $data = $res->json();
            if (!is_array($data)) {
                return $this->failed('rdap', 'RDAP response không hợp lệ', $res->body());
            }

            $expiresAt = null;
            foreach ((array) Arr::get($data, 'events', []) as $event) {
                if (is_array($event) && ($event['eventAction'] ?? null) === 'expiration') {
                    $expiresAt = $this->parseDate((string) ($event['eventDate'] ?? ''));
                    break;
                }
            }

            if ($expiresAt === null) {
                return $this->failed('rdap', 'RDAP không có event expiration', $res->body());
            }

            $nameservers = [];
            foreach ((array) Arr::get($data, 'nameservers', []) as $ns) {
                if (is_array($ns) && !empty($ns['ldhName'])) {
                    $nameservers[] = strtolower((string) $ns['ldhName']);
                }
            }

            return new DomainCheckResult(
                expiresAt: $expiresAt,
                registrar: $this->extractRdapRegistrar($data),
                nameservers: array_values(array_unique($nameservers)),
                method: 'rdap',
                error: null,
                raw: $res->body(),
            );
        } catch (\Throwable $e) {
            return $this->failed('rdap', $e->getMessage(), '');
        }
    }

    private function checkWhois(string $domain): DomainCheckResult
    {
        $tld = $this->extractTld($domain);
        $servers = (array) config('monitor.check.whois.servers', []);
        $server = $servers[$tld] ?? ('whois.nic.' . $tld);
        $timeout = (int) config('monitor.check.whois.timeout', 10);

        try {
            $fp = @fsockopen($server, 43, $errno, $errstr, $timeout);
            if ($fp === false) {
                return $this->failed('whois', "Không kết nối được whois server {$server} ({$errstr})", '');
            }

            stream_set_timeout($fp, $timeout);
            fwrite($fp, $domain . "\r\n");

            $raw = '';
            while (!feof($fp)) {
                $line = fgets($fp, 4096);
                if ($line === false) {
                    break;
                }
                $raw .= $line;
                if (strlen($raw) > 512 * 1024) {
                    break;
                }
            }
            fclose($fp);

            $expiresAt = $this->parseWhoisDate($raw);
            if ($expiresAt === null) {
                return $this->failed('whois', 'WHOIS không tìm thấy ngày hết hạn', $raw);
            }

            return new DomainCheckResult(
                expiresAt: $expiresAt,
                registrar: $this->parseWhoisRegistrar($raw),
                nameservers: $this->parseWhoisNameservers($raw),
                method: 'whois',
                error: null,
                raw: $raw,
            );
        } catch (\Throwable $e) {
            return $this->failed('whois', $e->getMessage(), '');
        }
    }

    private function checkThirdParty(string $domain): DomainCheckResult
    {
        $apiKey = (string) config('monitor.check.third_party.api_key', '');
        $endpoint = (string) config('monitor.check.third_party.endpoint', '');
        if ($apiKey === '' || $endpoint === '') {
            return $this->failed('third_party', 'Chưa cấu hình API key cho nguồn tra cứu bên thứ ba', '');
        }

        $endpoint = str_replace(['{domain}', '{apiKey}'], [$domain, $apiKey], $endpoint);

        try {
            $res = Http::timeout(15)->get($endpoint);
            if (!$res->successful()) {
                return $this->failed('third_party', 'API trả về HTTP ' . $res->status(), $res->body());
            }

            $data = $res->json();
            if (!is_array($data)) {
                return $this->failed('third_party', 'API response không hợp lệ', $res->body());
            }

            $record = Arr::get($data, 'WhoisRecord', $data);
            $expiresAt = $this->parseDate(
                (string) (Arr::get($record, 'registryData.expiresDate')
                    ?? Arr::get($record, 'expiresDate')
                    ?? Arr::get($record, 'expirationDate')
                    ?? '')
            );

            if ($expiresAt === null) {
                return $this->failed('third_party', 'API không trả về ngày hết hạn', $res->body());
            }

            $nameservers = Arr::get($record, 'nameServers', []);
            $nsList = [];
            foreach ((array) $nameservers as $ns) {
                if (is_array($ns)) {
                    $ns = $ns['hostName'] ?? $ns['name'] ?? '';
                }
                $ns = trim((string) $ns);
                if ($ns !== '') {
                    $nsList[] = strtolower($ns);
                }
            }

            return new DomainCheckResult(
                expiresAt: $expiresAt,
                registrar: (string) (Arr::get($record, 'registrarName') ?? Arr::get($record, 'registryData.registrar.name') ?? ''),
                nameservers: array_values(array_unique($nsList)),
                method: 'third_party',
                error: null,
                raw: $res->body(),
            );
        } catch (\Throwable $e) {
            return $this->failed('third_party', $e->getMessage(), '');
        }
    }

    private function failed(string $method, string $error, string $raw): DomainCheckResult
    {
        return new DomainCheckResult(
            expiresAt: null,
            registrar: null,
            nameservers: [],
            method: $method,
            error: $error,
            raw: $raw,
        );
    }

    private function extractTld(string $domain): string
    {
        $labels = explode('.', $domain);
        $last = end($labels);

        return strtolower((string) $last);
    }

    private function parseDate(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $data RDAP JSON
     */
    private function extractRdapRegistrar(array $data): ?string
    {
        foreach ((array) Arr::get($data, 'entities', []) as $entity) {
            if (!is_array($entity)) {
                continue;
            }
            $roles = (array) Arr::get($entity, 'roles', []);
            if (!in_array('registrar', $roles, true)) {
                continue;
            }

            $vcard = Arr::get($entity, 'vcardArray', []);
            if (!is_array($vcard) || count($vcard) < 2 || !is_array($vcard[1])) {
                continue;
            }

            foreach ($vcard[1] as $row) {
                if (is_array($row) && isset($row[0]) && strtolower((string) $row[0]) === 'fn') {
                    return (string) ($row[3] ?? '');
                }
            }
        }

        return null;
    }

    private function parseWhoisDate(string $raw): ?Carbon
    {
        $patterns = [
            '/registry\s+expiry\s+date\s*[:=]\s*(.+)$/im',
            '/registrar\s+registration\s+expiration\s+date\s*[:=]\s*(.+)$/im',
            '/domain\s+expiration\s+date\s*[:=]\s*(.+)$/im',
            '/expiration\s+date(?:time)?\s*[:=]\s*(.+)$/im',
            '/expir\w*\s*(?:date|time)\s*[:=]\s*(.+)$/im',
            '/paid-till\s*[:=]\s*(.+)$/im',
            '/expires\s+on\s*[:=]\s*(.+)$/im',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $raw, $m)) {
                $date = $this->parseDate(trim($m[1]));
                if ($date !== null) {
                    return $date;
                }
            }
        }

        return null;
    }

    private function parseWhoisRegistrar(string $raw): ?string
    {
        $patterns = [
            '/registrar\s*:\s*(.+)$/im',
            '/registrar\s+name\s*:\s*(.+)$/im',
            '/sponsoring\s+registrar\s*:\s*(.+)$/im',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $raw, $m)) {
                $value = trim($m[1]);
                if ($value !== '') {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function parseWhoisNameservers(string $raw): array
    {
        $ns = [];
        if (preg_match_all('/name\s+server\s*:\s*(.+)$/im', $raw, $matches)) {
            foreach ($matches[1] as $value) {
                $value = strtolower(trim($value));
                if ($value !== '') {
                    $ns[] = $value;
                }
            }
        }

        return array_values(array_unique($ns));
    }
}