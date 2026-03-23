<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Http\Responses\ApiResponse;
use App\Core\Http\Middleware\SetLocale;
use App\Core\Http\Middleware\RequireUserType;
use App\Core\Http\Middleware\ForceJsonAccept;
use App\Core\Http\Middleware\RequestId;
use App\Core\Http\Middleware\ResponseTime;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tạo trace id cho mỗi request /api/* để debug và trace giữa các service.
        $middleware->prependToGroup('api', RequestId::class);

        // Buộc API luôn "nhìn" như JSON để tránh redirect về trang login và trả về HTML.
        $middleware->prependToGroup('api', ForceJsonAccept::class);

        // Thoi gian xu ly response (ms) cho moi request /api/*.
        // (prepend sau cung de nam ngoai cung trong middleware stack)
        $middleware->prependToGroup('api', ResponseTime::class);

        // Cho phép đổi ngôn ngữ thông báo lỗi (vi/en) theo từng request.
        $middleware->appendToGroup('api', SetLocale::class);

        $middleware->alias([
            'user_type' => RequireUserType::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Buoc cac route /api/* luon render JSON ke ca client gui Accept: text/html (thuong gap khi goi tu browser).
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Khong log nhung loi API "du kien" (4xx do client) thanh local.ERROR.
        $exceptions->dontReportWhen(function (\Throwable $e) {
            return $e instanceof ApiException && $e->status < 500;
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::fail(
                data: [],
                code: ErrorCode::UNAUTHORIZED->value,
                message: __('messages.unauthorized'),
                status: 401,
            );
        });

        $exceptions->render(function (AuthorizationException $e) {
            return ApiResponse::fail(
                data: [],
                code: ErrorCode::FORBIDDEN->value,
                message: __('messages.forbidden'),
                status: 403,
            );
        });

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

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            $allow = $e->getHeaders()['Allow'] ?? null;

            return ApiResponse::fail(
                data: $allow ? ['allow' => $allow] : [],
                code: ErrorCode::METHOD_NOT_ALLOWED->value,
                message: __('messages.method_not_allowed'),
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

        // Fallback cuoi cho API: khong bao gio tra ve trang loi HTML.
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: __('messages.internal_error'),
                code: ErrorCode::INTERNAL_ERROR->value,
                status: 500,
            );
        });
    })->create();
