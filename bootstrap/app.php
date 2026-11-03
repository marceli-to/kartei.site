<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // SPA authentication via Sanctum session cookies on /api
        $middleware->statefulApi();
        // 600 requests/minute, see the "api" limiter in AppServiceProvider
        $middleware->throttleApi();

        // Login route is named "auth.login" (routes/auth.php)
        $middleware->redirectGuestsTo(fn () => route('auth.login'));
        $middleware->redirectUsersTo(fn () => config('app.home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Policy failures reach render callbacks as AccessDeniedHttpException
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson() && $e->getPrevious() instanceof AuthorizationException) {
                return response()->json([
                    'message' => 'You are not authorized to perform this action.',
                ], Response::HTTP_FORBIDDEN);
            }
        });
    })->create();
