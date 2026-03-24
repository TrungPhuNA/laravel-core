<?php

namespace App\Core\Http\Middleware;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

final class RequirePermission
{
    /**
     * @param Closure(Request): mixed $next
     */
    public function handle(Request $request, Closure $next, string ...$params)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user() ?? $request->user('sanctum');

        if (!$user) {
            throw new ApiException(
                errorCode: ErrorCode::UNAUTHORIZED->value,
                message: __('messages.unauthorized'),
                status: 401,
            );
        }

        $email = strtolower(trim((string) ($user->email ?? '')));
        $superAdmins = (array) config('core.rbac.super_admin_emails', []);
        if ($email !== '' && in_array($email, $superAdmins, true)) {
            return $next($request);
        }

        $raw = implode(',', $params);
        $parts = preg_split('/[,\|]+/', $raw) ?: [];
        $names = array_values(array_filter(array_map(
            static fn ($v) => trim((string) $v),
            $parts,
        ), static fn ($v) => $v !== ''));

        if ($names === []) {
            return $next($request);
        }

        $guard = (string) config('core.rbac.guard', 'sanctum');

        // Tranh PermissionDoesNotExist (spatie se throw neu permission chua duoc seed).
        $existing = Permission::query()
            ->where('guard_name', $guard)
            ->whereIn('name', $names)
            ->pluck('name')
            ->all();

        if ($existing === []) {
            throw new ApiException(
                errorCode: ErrorCode::FORBIDDEN->value,
                message: __('messages.forbidden'),
                status: 403,
            );
        }

        if (!$user->hasAnyPermission($existing)) {
            throw new ApiException(
                errorCode: ErrorCode::FORBIDDEN->value,
                message: __('messages.forbidden'),
                status: 403,
            );
        }

        return $next($request);
    }
}

