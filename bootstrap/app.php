<?php

use Illuminate\Database\QueryException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\EnsureSsoSession::class,
        ]);
        $middleware->alias([
            'cekAuth' => \App\Http\Middleware\CekAuth::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Database proyek belum memakai tabel cache_locks. File cache tetap
        // memberikan mutex yang aman untuk satu server scheduler.
        $schedule->useCache('file');
        $schedule->command('shift:remind-unmapped')
            ->dailyAt('07:00')
            ->withoutOverlapping();
        $schedule->command('ticket:auto-close-unresponded')
            ->dailyAt('08:00')
            ->withoutOverlapping();
        $schedule->command('karyawan:nonaktif')
            ->monthlyOn(22, '01:00')
            ->withoutOverlapping();
    })
    //  ->monthlyOn(16, '11:39')
    // ->timezone('Asia/Jakarta');

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (QueryException | PDOException $e, $request) {

            // Tangkap error DB connection
            if (
                str_contains($e->getMessage(), 'could not connect to server') ||
                str_contains($e->getMessage(), 'Connection timed out')
            ) {

                // AJAX / API
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sistem sedang sibuk, silakan coba lagi.'
                    ], 503);
                }

                // WEB
                return response()->view(
                    'errors.database',
                    [],
                    503
                );
            }
        });
    })->create();
