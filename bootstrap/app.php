<?php

use App\Http\Middleware\PreventBackHistoryCache;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Trust Proxies
        |--------------------------------------------------------------------------
        |
        | Hosting produksi berjalan di belakang reverse proxy panel hosting
        | (Nginx/panel-managed). Tanpa ini, Laravel tidak tahu request
        | asli HTTPS, sehingga Request::isSecure(), asset()/url() (bisa
        | menghasilkan http:// pada aset -> mixed content setelah reload),
        | serta cookie sesi "secure" bisa salah deteksi khusus di hosting
        | (lihat SESSION_SECURE_COOKIE di .env). IP proxy panel hosting
        | tidak diketahui/bisa berubah, jadi percayai semua proxy ('*').
        |--------------------------------------------------------------------------
        */

        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->web(
            append: [
                PreventBackHistoryCache::class,
            ]
        );

        // Laravel 11+ no longer applies throttle:api to the api group by
        // default -- restore it so every /api/* route has a rate limit.
        $middleware->throttleApi();

        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'is_user' => \App\Http\Middleware\IsUser::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
