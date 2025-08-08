<?php

namespace App\Exceptions;

use App\Models\ErrorLog;
use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    protected $levels = [
        //
    ];

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

//    public function render($request, Throwable $exception)
//    {
//        try {
//            ErrorLog::create([
//                'message' => $exception->getMessage(),
//                'file' => $exception->getFile(),
//                'line' => $exception->getLine(),
//                'trace' => $exception->getTraceAsString(),
//                'user_id' => auth()->id(),
//            ]);
//        } catch (\Throwable $e) {
//        }
//
//        if ($request->expectsJson()) {
//            $status = $this->getStatusCode($exception);
//
//            return ResponseHelper::error(
//                $status === 401 ? 'Unauthenticated.' : 'A server error occurred. Please try again later.',
//                $status
//            );
//        }
//
//        return parent::render($request, $exception);
//    }
//
//    protected function unauthenticated($request, AuthenticationException $exception)
//    {
//        if ($request->expectsJson()) {
//            return ResponseHelper::error('Unauthenticated.', 401);
//        }
//
//        return redirect()->guest(route('login'));
//    }
//
    private function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    public function render($request, Throwable $exception)
    {
        try {
            ErrorLog::create([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
        } catch (\Throwable $e) {
            // Logging error should not break the app
        }

        if ($request->expectsJson()) {

            // Validation errors
            if ($exception instanceof ValidationException) {
                return ResponseHelper::error(
                    'Validation failed.',
                    422,
                    $exception->errors() // returns the array of validation messages
                );
            }

            // Authentication errors
            if ($exception instanceof AuthenticationException) {
                return ResponseHelper::error('Unauthenticated.', 401);
            }

            // All other errors (backend/server)
            return ResponseHelper::error(
                'A server error occurred. Please try again later.',
                $this->getStatusCode($exception)
            );
        }

        // Default for non-JSON requests (e.g., web)
        return parent::render($request, $exception);
    }
}
