<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException as TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof TokenMismatchException && isset($request->_form_origin) && $request->_form_origin == 'login') {
            /* Custom Log */
            $date = date('Y-m-d');
            $json_request = json_encode($request->all());
            $content = '(' . date('Y-m-d H:i:s') . ') ' . "{$json_request}" . PHP_EOL;
            file_put_contents( storage_path("/logs/login-{$date}.log"), $content, FILE_APPEND );
            
            return redirect('login')
                ->withErrors(['unexp_err' => 'The system encounters unexpected issue. Please sign in back now or later.']);
        }
            
        return parent::render($request, $e);
    }
}
