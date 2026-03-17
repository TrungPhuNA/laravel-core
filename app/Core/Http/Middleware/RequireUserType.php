<?php

namespace App\Core\Http\Middleware;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\UserType;
use Closure;
use Illuminate\Http\Request;

final class RequireUserType
{
    public function handle(Request $request, Closure $next, string ...$allowedTypes)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if (!$user) {
            throw new ApiException(
                errorCode: ErrorCode::UNAUTHORIZED->value,
                message: __('messages.unauthorized'),
                status: 401,
            );
        }

        $current = $user->user_type;
        $currentValue = $current instanceof UserType ? $current->value : (string) $current;

        $allowed = array_map(static fn (string $t) => strtoupper(trim($t)), $allowedTypes);

        if (!in_array(strtoupper($currentValue), $allowed, true)) {
            throw new ApiException(
                errorCode: ErrorCode::FORBIDDEN->value,
                message: __('messages.forbidden'),
                status: 403,
            );
        }

        return $next($request);
    }
}

