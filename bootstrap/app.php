<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO | \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $redirectToLogin = function () {
            return redirect()->route('login')
                ->with('error', 'セッションの有効期限が切れたため、再度ログインしてください。');
        };

        $exceptions->render(function (TokenMismatchException $e, $request) use ($redirectToLogin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'セッションの有効期限が切れました。ページを再読み込みしてください。',
                ], 419);
            }

            return $redirectToLogin();
        });

        $exceptions->render(function (HttpException $e) use ($redirectToLogin) {
            if ($e->getStatusCode() === 419) {
                return $redirectToLogin();
            }
        });
    })->create();
