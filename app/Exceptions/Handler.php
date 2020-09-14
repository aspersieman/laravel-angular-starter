<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use Dotenv\Exception\ValidationException;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponder;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(\Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $exception)
    {
        if($exception instanceof ValidationException){

            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if($exception instanceof ModelNotFoundException){
            $model_name = class_basename($exception->getModel());

            return $this->errorResponse("{$model_name} not found", 404);
        }

        if($exception instanceof MethodNotAllowedHttpException){
            $message = "Request method is invalid";
            $code    = 405;

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof \ReflectionException){
            $message = $exception->getMessage();
            $code    = 500;

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof NotFoundHttpException){
            $message = "URL not found";
            $code    = 404;

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof \HttpException){
            $message = $exception->getMessage();
            $code    = $exception->getCode();
            return $this->errorResponse($message, $code);
        }

        if($exception instanceof TokenMismatchException){
            return redirect()->back()->withInput($request->input());
        }

        if($exception instanceof RelationNotFoundException){
            $message = $exception->getMessage();
            $code    = 500;

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof \ErrorException){
            $message = $exception->getMessage();
            $code    = 500;

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof \BadMethodCallException){
            $message = $exception->getMessage();
            $code    = 500;

            return $this->errorResponse($message, $code);
        }


        if($exception instanceof \Swift_TransportException){
            $message = $exception->getMessage();
            $code    = 500;

            return $this->errorResponse($message, $code);
        }

        return parent::render($request, $exception);
    }
}
