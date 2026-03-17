<?php

namespace Modules\Auth\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'avatar_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:20'],

            'address_line1' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_line2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'ward' => ['sometimes', 'nullable', 'string', 'max:255'],
            'district' => ['sometimes', 'nullable', 'string', 'max:255'],
            'province' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country' => ['sometimes', 'nullable', 'string', 'size:2'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],

            'company' => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'timezone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'locale' => ['sometimes', 'nullable', 'string', 'max:10'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => ['description' => 'User display name.', 'example' => 'Demo User'],
            'phone' => ['description' => 'Phone number.', 'example' => '0900000000'],
            'avatar_url' => ['description' => 'Avatar image URL.', 'example' => 'https://example.com/avatar.png'],
            'date_of_birth' => ['description' => 'Date of birth (YYYY-MM-DD).', 'example' => '1990-01-01'],
            'gender' => ['description' => 'Gender (free text).', 'example' => 'male'],
            'address_line1' => ['description' => 'Address line 1.', 'example' => '123 Street'],
            'address_line2' => ['description' => 'Address line 2.', 'example' => 'Apt 4'],
            'ward' => ['description' => 'Ward.', 'example' => 'Ward 1'],
            'district' => ['description' => 'District.', 'example' => 'District 1'],
            'province' => ['description' => 'Province/City.', 'example' => 'HCM'],
            'country' => ['description' => 'ISO 3166-1 alpha-2 country code.', 'example' => 'VN'],
            'postal_code' => ['description' => 'Postal code.', 'example' => '700000'],
            'company' => ['description' => 'Company name.', 'example' => 'Core Co'],
            'job_title' => ['description' => 'Job title.', 'example' => 'Engineer'],
            'timezone' => ['description' => 'Timezone identifier.', 'example' => 'Asia/Ho_Chi_Minh'],
            'locale' => ['description' => 'Locale code.', 'example' => 'vi'],
            'bio' => ['description' => 'Short bio.', 'example' => 'Hello'],
        ];
    }
}
