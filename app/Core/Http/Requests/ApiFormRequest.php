<?php

namespace App\Core\Http\Requests;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base FormRequest cho API.
 *
 * - Tự động trim string input (trừ password để tránh side-effect).
 * - Có helper parse query params chuẩn cho list endpoint.
 */
abstract class ApiFormRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Tránh trim password để không làm thay đổi input người dùng.
        $skipKeys = ['password', 'password_confirmation'];

        $this->replace($this->trimRecursive($data, $skipKeys));
    }

    public function apiQueryParams(): ApiQueryParams
    {
        return ApiQueryParams::fromRequest($this);
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string> $skipKeys
     * @return array<string, mixed>
     */
    private function trimRecursive(array $data, array $skipKeys): array
    {
        $out = [];

        foreach ($data as $key => $value) {
            $keyStr = (string) $key;

            if (in_array($keyStr, $skipKeys, true)) {
                $out[$keyStr] = $value;
                continue;
            }

            if (is_string($value)) {
                $out[$keyStr] = trim($value);
                continue;
            }

            if (is_array($value)) {
                /** @var array<string, mixed> $value */
                $out[$keyStr] = $this->trimRecursive($value, $skipKeys);
                continue;
            }

            $out[$keyStr] = $value;
        }

        return $out;
    }
}

