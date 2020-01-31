<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use ES\Log\FluentLogger;
class Handler extends ExceptionHandler
{
    /**
     * 自定义捕获异常后，上报等级
     * */
    protected static $reportLevel = 'error';
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }
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
        if ($this->shouldntReport($exception)) {
            return;
        }

        if (is_callable($reportCallable = [$exception, 'report'])) {
            return $this->container->call($reportCallable);
        }

        $trace = array_slice($exception->getTrace(), 0, 10);
        $reportLevel = self::$reportLevel;
        FluentLogger::$reportLevel('system', [
            'msg' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => $trace,
        ]);
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
}
