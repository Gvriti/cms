<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
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
        return parent::report($e);
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
     * {@inheritdoc}
     */
    protected function renderHttpException(HttpException $e)
    {
        $status = $e->getStatusCode();

        if ($this->request->ajax()) {
            return response()->make(trans('http.' . $status), $status);
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

        if ($this->request->ajax()) {
            if ($debug) {
                return response()->make(
                    $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine(), $status
                );
            }

            return response()->make(trans('http.' . $status), $status);
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
        $dir = cms_will_load() ? 'admin' : 'site';

        if (view()->exists($dir . ".errors.{$status}")) {
            return response()->view($dir . ".errors.{$status}", ['exception' => $e], $status);
        }

        return false;
    }
}