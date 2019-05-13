<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('/');
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHttpException(HttpExceptionInterface $exception)
    {
        $status = $exception->getStatusCode();

        if (request()->expectsJson()) {
            if (($trans = trans('http.' . $status)) !== 'http.' . $status) {
                return response($trans, $status);
            } else {
                return response($exception->getMessage(), $status);
            }
        }

        if ($view = $this->getExceptionView($status, $exception)) {
            return $view;
        }

        return $this->convertExceptionToResponse($exception, true);
    }

    /**
     * {@inheritdoc}
     */
    protected function convertExceptionToResponse(Exception $exception, $viewChecked = false)
    {
        $response = parent::convertExceptionToResponse($exception);

        $status = $response->getStatusCode();

        $debug = config('app.debug');

        if (request()->expectsJson()) {
            if ($debug) {
                return response()->make(
                    $exception->getMessage() . ' in ' . $exception->getFile() . ' line ' . $exception->getLine(), $status
                );
            }

            if (($trans = trans('http.' . $status)) !== 'http.' . $status) {
                return response($trans, $status);
            } else {
                return response($exception->getMessage(), $status);
            }
        }

        if (! $debug && ! $viewChecked && ($view = $this->getExceptionView($status, $exception))) {
            return $view;
        }

        return $response;
    }

    /**
     * Get the view for the given exception.
     *
     * @param  string  $status
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|bool
     */
    protected function getExceptionView($status, $exception)
    {
        $dir = cms_is_booted() ? 'admin' : 'web';

        if (view()->exists($dir . ".errors.{$status}")) {
            return response()->view($dir . ".errors.{$status}", ['exception' => $exception], $status);
        }

        return false;
    }
}
