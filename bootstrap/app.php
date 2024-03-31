<?php

use Illuminate\Foundation\Application;
use Filament\Notifications\Notification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $tempPath = storage_path('app/livewire-tmp');
        $schedule->exec("rm -r {$tempPath}")
            ->daily()
            ->between('2:00', '5:00');

        $schedule->command('backup:run')
            ->daily()
            ->between('2:00', '5:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            \Entensy\FilamentTracer\FilamentTracer::capture($e, request());
        });

        $exceptions->render(function (\Exception $e, $request) {
            if ($e instanceof AuthenticationException) {
                return redirect('/login');
            }

            if ($e instanceof NotFoundHttpException) {
                return redirect('/login');
            }

            if ($e instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                return redirect('/dashboard');
            }

            if (\method_exists($e, 'getStatusCode')) {
                $code = $e->getStatusCode();

                if (\in_array($code, [401, 403, 404, 500, 503])) {
                    return response()->view("errors.{$code}");
                } else {
                    return response()->view('errors.default');
                }
            }

            if (app()->environment('local', 'staging')) {
                // TODO: do some other errorring?!
                $traces = collect(debug_backtrace())
                    ->transform(
                        fn ($t) => ("{$t['line']} : {$t['file']}")
                    )
                    ->join('<div><div>');
                echo $traces;

                dd(new \ReflectionClass($e), $e->getLine(), $e->getMessage(), $e->getFile());
            }

            Notification::make()
                ->warning()
                ->title(__('notify.logs.title'))
                ->body(__('notify.logs.message'))
                ->persistent()
                ->send();

            return redirect('/dashboard');
        });
    })->create();
