<?php

namespace App\Exceptions;

use Exception;
use App\ErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
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

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        // Custom Error Log (DB)
        $errorLog = new ErrorLog();
        $errorLog->fullUrl = $request->fullUrl();
        $errorLog->code = json_encode($exception->getCode());
        $errorLog->file = json_encode($exception->getFile());
        $errorLog->line = json_encode($exception->getLine());
        $errorLog->message = json_encode($exception->getMessage());
        $errorLog->save();

        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 404) {
                return redirect(route('mail.404'));
            }
        }
        return parent::render($request, $exception);
    }

}
