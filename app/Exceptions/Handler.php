<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

//added my Malindi John
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
//ends here

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
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException && $request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($exception instanceof RouteNotFoundException || $exception instanceof HttpException) {
            // Handle the RouteNotFoundException when the "login" route is not accessible.
            // You can return a custom response or redirect to another route here.
            // return response()->json(['error' => 'Route not found or accessible'], 404);
            return response()->json(
                [
                    'status'=> 401,
                    'error_message' => "Whoops! User token is expired or not provided",
                    'error' => 'Unauthenticated'
                ], 401);
        }

        return parent::render($request, $exception);
    }
}
