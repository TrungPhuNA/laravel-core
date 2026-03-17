<?php

namespace Modules\Auth\Http\Resources\Api\V1;

use App\Core\Support\UserType;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $userType = $this->user_type;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'user_type' => $userType instanceof UserType ? $userType->value : (string) $userType,

            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url,
            'date_of_birth' => optional($this->date_of_birth)->toDateString(),
            'gender' => $this->gender,

            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'ward' => $this->ward,
            'district' => $this->district,
            'province' => $this->province,
            'country' => $this->country,
            'postal_code' => $this->postal_code,

            'company' => $this->company,
            'job_title' => $this->job_title,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'bio' => $this->bio,

            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
