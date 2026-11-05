<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPermission;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: ['127.0.0.1', '::1'], headers: Request::HEADER_X_FORWARDED_PROTO);
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, Throwable $e, Request $request) {
            if ($response->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Sesi telah kedaluwarsa. Silakan refresh halaman.'], 419);
                }
                return redirect()->route('login')->with('error', 'Sesi Anda telah kedaluwarsa atau halaman terlalu lama dibuka. Silakan coba login kembali.');
            }
            return $response;
        });
    })->create();
