<?php

namespace Modules\Ecommerce\Http\Controllers\Api\V1\Admin;

use App\Core\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Routing\Controller;

/**
 * @group Ecommerce
 * @subgroup Admin - User Lookup
 */
final class UserLookupController extends Controller
{
    public function index()
    {
        $q = trim((string) request()->query('q', ''));
        $limit = (int) request()->query('limit', 20);
        $limit = max(1, min($limit, 100));

        $query = User::query()->select(['id', 'name', 'email', 'user_type']);

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('email', 'like', '%'.$q.'%')
                    ->orWhere('name', 'like', '%'.$q.'%')
                    ->orWhere('phone', 'like', '%'.$q.'%');
            });
        }

        $items = $query->orderBy('id')->limit($limit)->get()->map(function (User $u) {
            return [
                'id' => (int) $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'user_type' => $u->user_type?->value ?? (string) $u->user_type,
            ];
        })->values();

        return ApiResponse::success(
            data: ['items' => $items],
            code: 'ECM_USER_LOOKUP_SUCCESS',
            message: 'Tìm user thành công',
        );
    }
}

