<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Http\Responses\ApiResponse;
use App\Core\Http\Middleware\SetLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Allow switching validation/error messages between vi/en per request.
        $middleware->appendToGroup('api', SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ApiException $e) {
            if ($e->status >= 500) {
                return ApiResponse::error(
                    message: $e->getMessage(),
                    code: $e->errorCode,
                    data: $e->details ?: null,
                    status: $e->status,
                );
            }

            return ApiResponse::fail(
                data: $e->details,
                code: $e->errorCode,
                message: $e->getMessage(),
                status: $e->status,
            );
        });

        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::fail(
                data: $e->errors(),
                code: ErrorCode::VALIDATION_ERROR->value,
                message: __('messages.validation_error'),
                status: 400,
            );
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            return ApiResponse::fail(
                data: [],
                code: ErrorCode::NOT_FOUND->value,
                message: __('messages.not_found'),
                status: 404,
            );
        });
    })->create();
