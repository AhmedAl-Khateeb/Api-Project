<?php

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // permission exception
        $exceptions->render(function (UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return app(ApiResponse::class)->forbidden('ليس لديك صلاحية');
            }
        });

        // authentication exception
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return app(ApiResponse::class)->unauthorized('غير مسجل الدخول');
            }
        });

        // Validation errors
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return app(ApiResponse::class)->validationError(
                    $e->errors(),
                    'خطأ في التحقق من البيانات'
                );
            }
        });
    })->create();
