<?php

namespace App\Exceptions;

use App\Jobs\SendExceptionMailJob;
use App\Mail\ExceptionMail;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        ValidationException::class,
        NotFoundHttpException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            try
            {
                $exception = [
                    "Exception" => get_class($e),
                    "URL" => url()->current(),
                    "Message" => $e->getMessage(),
                    "File" => $e->getFile(),
                    "Line" => $e->getLine(),
                    "Time" => (string) Carbon::now(),
                    // "Trace" => $e->getTraceAsString(),
                ];

                // Log::channel('slack')->critical('error', $exception);
                // Mail::to("elsayedfeteh@gmail.com")->send(new ExceptionMail($exception));

                dispatch(new SendExceptionMailJob($exception, 'path'));
            }
            catch (Throwable $e)
            {
                Log::error($e);
            }
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson())
            {
                return new JsonResponse([
                    "message" => "You must be login!",
                ], 401);
            }
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson())
            {
                return new JsonResponse([
                    'message' => 'Validation Error',
                    'data' => array_values($e->errors()),
                ], 422);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson())
            {
                return new JsonResponse([
                    'message' => 'Not Found'
                ]);
            }
        });
    }
}
