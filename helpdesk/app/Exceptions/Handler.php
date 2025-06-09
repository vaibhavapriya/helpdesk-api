<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Route;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // Add any exceptions you do not want to log here
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        // Log the error to the database
        if ($this->shouldReport($exception)) {
            $userId = Auth::check() ? Auth::id() : null; // User ID if logged in
            $method = request()->method(); // HTTP method
            $route = optional(Route::current())->getName() ?? request()->path(); // Current route

            // Store the error in the error_logs table
            ErrorLog::create([
                'error_message' => $exception->getMessage(),
                'stack_trace' => $exception->getTraceAsString(),
                'user_id' => $userId,
                'method' => $method,
                'route' => $route,
            ]);
        }

        // Call the parent report method
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle different types of exceptions, you can return a custom response
        return parent::render($request, $exception);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param  \Throwable  $exception
     * @return bool
     */
    public function shouldReport(Throwable $exception)
    {
        // By default, we report all exceptions. You can filter this based on custom conditions.
        return parent::shouldReport($exception);
    }
}
