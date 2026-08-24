<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // All routes currently protected by the "auth" middleware live under
        // /admin, so unauthenticated visitors are always sent to the admin
        // login page, and an already-authenticated admin hitting a "guest"
        // route (e.g. /admin/login) is sent to the dashboard.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));

        // Iterasi 18 (Fase 4): mode preview/draft harus berlaku site-wide
        // (publik & admin, karena admin butuh set/clear penanda preview via
        // ?preview=1|0 di halaman publik manapun) — ditambahkan ke grup
        // "web" (satu-satunya grup routing yang dipakai project ini, lihat
        // withRouting() di atas), bukan cuma dipasang di routes/web.php.
        $middleware->web(append: [
            \App\Http\Middleware\HandleAppearancePreview::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
