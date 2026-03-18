<?php

namespace Modules\User\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\User $resource
 */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type?->value ?? (string) $user->user_type,

            // Profile
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
            'date_of_birth' => $user->date_of_birth?->toDateString(),
            'gender' => $user->gender,
            'address_line1' => $user->address_line1,
            'address_line2' => $user->address_line2,
            'ward' => $user->ward,
            'district' => $user->district,
            'province' => $user->province,
            'country' => $user->country,
            'postal_code' => $user->postal_code,
            'company' => $user->company,
            'job_title' => $user->job_title,
            'timezone' => $user->timezone,
            'locale' => $user->locale,
            'bio' => $user->bio,

            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
            'deleted_at' => $user->deleted_at?->toISOString(),
        ];
    }
}

