<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $this->request = $request;

        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        return parent::render($request, $e);
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

        return redirect()->guest('login');
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHttpException(HttpException $e)
    {
        $status = $e->getStatusCode();

        if ($this->request->expectsJson()) {
            if (($trans = trans('http.' . $status)) !== 'http.' . $status) {
                return response($trans, $status);
            } else {
                return response($e->getMessage(), $status);
            }
        }

        if ($view = $this->getExceptionView($status, $e)) {
            return $view;
        }

        return $this->convertExceptionToResponse($e, true);
    }

    /**
     * {@inheritdoc}
     */
    protected function convertExceptionToResponse(Exception $e, $viewChecked = false)
    {
        $response = parent::convertExceptionToResponse($e);

        $status = $response->getStatusCode();

        $debug = config('app.debug');

        if ($this->request->expectsJson()) {
            if ($debug) {
                return response()->make(
                    $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine(), $status
                );
            }

            if (($trans = trans('http.' . $status)) !== 'http.' . $status) {
                return response($trans, $status);
            } else {
                return response($e->getMessage(), $status);
            }
        }

        if (! $debug && ! $viewChecked && ($view = $this->getExceptionView($status, $e))) {
            return $view;
        }

        return $response;
    }

    /**
     * Get the view for the given exception.
     *
     * @param  string  $status
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response|bool
     */
    protected function getExceptionView($status, $e)
    {
        $dir = cms_is_booted() ? 'admin' : 'site';

        if (view()->exists($dir . ".errors.{$status}")) {
            return response()->view($dir . ".errors.{$status}", ['exception' => $e], $status);
        }

        return false;
    }
}
