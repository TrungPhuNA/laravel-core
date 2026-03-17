<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Http\Responses\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ApiException $e) {
            return ApiResponse::error(
                code: $e->errorCode,
                message: $e->getMessage(),
                details: $e->details,
                status: $e->status,
            );
        });

        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::error(
                code: ErrorCode::VALIDATION_ERROR->value,
                message: 'Validation error',
                details: $e->errors(),
                status: 422,
            );
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            return ApiResponse::error(
                code: ErrorCode::NOT_FOUND->value,
                message: 'Not found',
                status: 404,
            );
        });
    })->create();
