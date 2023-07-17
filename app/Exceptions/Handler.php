<?php

namespace App\Exceptions;

use BezhanSalleh\FilamentExceptions\FilamentExceptions;
use Filament\Notifications\Notification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (\Throwable $e) {
            if ($this->shouldReport($e)) {
                FilamentExceptions::report($e);
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
                ->title(__('labels.logs.notify.title'))
                ->body(__('labels.logs.notify.message'))
                ->persistent()
                ->send();

            return redirect('/dashboard');
        });
    }
}
