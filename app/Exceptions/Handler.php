<?php

namespace App\Exceptions;

use Throwable;
use Filament\Notifications\Notification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldReport($e)) {
                \Entensy\FilamentTracer\FilamentTracer::capture($e, request());
            }
        });

        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return redirect('/dashboard');
        });

        $this->renderable(function (\Exception $e, $request) {
            if ($e instanceof AuthenticationException) {
                return redirect('/login');
            }

            if ($e instanceof NotFoundHttpException) {
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
    }
}
