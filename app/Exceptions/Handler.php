<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

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
        if ($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof ModelNotFoundException){
//            $modelName = $exception->getModel();
//            getting the class name without the 'APP'
            $modelName = strtolower(class_basename($exception->getModel()));

            return $this->errorResponse("Does not exist any {$modelName} with the specified identificator", 404);
        }
        if ($exception instanceof AuthenticationException){
            return $this->unauthenticated($request, $exception);
        }
        if ($exception instanceof AuthorizationException){
            return $this->errorResponse($exception->getMessage(), 403);
        }
        if ($exception instanceof NotFoundHttpException){
            return $this->errorResponse("The specified URL cannot be found", 404);
        }
//        trying to execute a maybe POST method to a class that was not defined/set
        if ($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse("The specified method for the request is invalid", 405);
        }
        if ($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
//        error handling of conflicts(when 2 classes are tied together e.g users( has both buyers and sellers)
//        can't delete a buyer directly
//        code 409 -- Conflict
        if ($exception instanceof QueryException){
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1451){
                return $this->errorResponse('Cannot remove this resource permanently. It is related 
                with any other resource', 409);
            }
        }
//        handling unexpected exceptions. for example: If the database is down and you try to fetch all users/buyers
//        Any error that is outside the above conditions is said to be an unexpected exception
//        code 500 -- Server error
//        We need to return a complete details of the error when in debug mode, so we use a condition
        if (config('app.debug')){
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected Exception. Try Later', 500);

    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('Unauthenticated', 401);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);
    }

}
