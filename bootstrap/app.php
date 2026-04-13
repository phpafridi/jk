<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Show friendly page for any unhandled exception (non-API, non-debug)
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson()) return null;
            if (config('app.debug')) return null; // let Ignition handle it in debug mode

            $status  = 500;
            $title   = 'Something went wrong';
            $message = 'An unexpected error occurred. Please try again or contact support.';

            if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
                $status  = 404;
                $title   = 'Page Not Found';
                $message = 'The page or record you are looking for does not exist.';
            } elseif ($e instanceof AuthenticationException) {
                return redirect()->guest(route('login'));
            } elseif ($e instanceof ValidationException) {
                return null; // let Laravel handle validation redirects normally
            } elseif ($e instanceof HttpException) {
                $status  = $e->getStatusCode();
                $title   = 'Error ' . $status;
                $message = $e->getMessage() ?: 'An error occurred. Please go back and try again.';
            }

            // Map some common DB/logic errors to readable messages
            $errMsg = $e->getMessage();
            if (str_contains($errMsg, 'Duplicate entry') || str_contains($errMsg, 'UNIQUE constraint')) {
                $title   = 'Duplicate Record';
                $message = 'This record already exists (e.g. a user or entry with the same CNIC/email). Please check and try again.';
                $status  = 422;
            } elseif (str_contains($errMsg, 'foreign key constraint') || str_contains($errMsg, 'FOREIGN KEY')) {
                $title   = 'Cannot Complete Action';
                $message = 'This record is linked to other data and cannot be deleted or modified directly. Remove the related records first.';
                $status  = 422;
            } elseif (str_contains($errMsg, 'Connection refused') || str_contains($errMsg, 'SQLSTATE')) {
                $title   = 'Database Error';
                $message = 'There was a problem connecting to the database. Please contact your administrator.';
            }

            return response()->view('errors.friendly', [
                'status'  => $status,
                'title'   => $title,
                'message' => $message,
            ], $status);
        });

    })->create();
