<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Session\TokenMismatchException; // ✅ IMPORT UNTUK 419
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Inertia\Inertia; // Tambahkan ini jika belum ada
use Illuminate\Http\Request; // Tambahkan ini jika belum ada

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // ✅ Alias untuk Spatie Role & Permission
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 🚩 1. PENANGANAN TOKENMISMATCHEXCEPTION (419 Page Expired)
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            
            // Jika request datang dari Inertia/AJAX, redirect kembali dengan pesan error
            if ($request->wantsJson() || $request->inertia()) {
                return redirect()
                    ->back()
                    ->with('error', 'Sesi Anda telah berakhir. Harap refresh halaman (Ctrl+R) dan coba lagi.');
            }
            
            // Jika bukan Inertia/AJAX, biarkan penanganan default Laravel
            return false;
        });
        
        // ✅ AuthorizationException (403 Forbidden)
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '⚠️ Anda tidak punya izin untuk melakukan aksi ini.'
                ], 403);
            }
            return back()->with('error', '⚠️ Anda tidak punya izin untuk melakukan aksi ini.');
        });

        // ✅ ModelNotFoundException (404 data tidak ditemukan)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '❌ Data tidak ditemukan.'
                ], 404);
            }
            return redirect()->route('dashboard')->with('error', '❌ Data tidak ditemukan.');
        });

        // ✅ NotFoundHttpException (404 halaman tidak ada)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => '❌ Halaman tidak ditemukan.'
                ], 404);
            }
            return Inertia::render('Errors/NotFound', [
                'message' => 'Halaman yang Anda cari tidak ditemukan.'
            ])->toResponse($request)->setStatusCode(404);
        });
    })
    ->create();