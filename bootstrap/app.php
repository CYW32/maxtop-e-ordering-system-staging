<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Only override in production (when APP_DEBUG is false) and for regular web requests
            if (!config('app.debug') && !$request->expectsJson()) {
                // CRITICAL FIX: Let Laravel handle Form Validation and Authentication normally!
                if ($e instanceof \Illuminate\Validation\ValidationException || $e instanceof \Illuminate\Auth\AuthenticationException) {
                    return null; // Returning null tells Laravel to ignore our custom page and use normal behavior!
                }

                $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface ? $e->getStatusCode() : 500;

                // 1. Detect Database Connection Errors
                if ($e instanceof \PDOException || $e instanceof \Illuminate\Database\QueryException) {
                    if (str_contains($e->getMessage(), 'SQLSTATE[HY000]') || str_contains($e->getMessage(), 'Connection refused')) {
                        return response()->view('errors.network', [], 500);
                    }
                }

                // 2. Handle 419 Page Expired
                if ($status === 419) {
                    return response()->view('errors.419', [], 419);
                }

                // 3. Handle 403 Spatie Permission / Access Denied
                if ($status === 403 || $e instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                    return response()->view('errors.403', [], 403);
                }

                // 4. Show the custom error page for ALL other errors
                return response()->view('errors.500', [], $status);
            }

            return null;
        });
    })
    ->create();
