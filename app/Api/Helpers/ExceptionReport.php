<?php

namespace App\Api\Helpers;

use App\Exceptions\ExceptionCode;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\RpcRequestException;
use App\Exceptions\SqlRunException;
use App\Exceptions\ThirdPartyRequestException;
use App\Exceptions\WeappRequestException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Support\Facades\Log;

class ExceptionReport
{
    use ApiResponse;

    /**
     * @var Throwable
     */
    public $throwable;
    /**
     * @var Request
     */
    public $request;

    /**
     * @var
     */
    protected $report;

    /**
     * ExceptionReport constructor.
     * @param Request $request
     * @param Throwable $throwable
     */
    function __construct(Request $request, Throwable $throwable)
    {
        $this->request = $request;
        $this->throwable = $throwable;
    }

    /**
     * @var array
     */
    //当抛出这些异常时，可以使用我们定义的错误信息与HTTP状态码
    //可以把常见异常放在这里
    public $doReport = [
        AuthenticationException::class => [],
        ModelNotFoundException::class => ['该模型未找到', ExceptionCode::COMMON_EXCEPTION, 404],
        AuthorizationException::class => ['没有此权限', ExceptionCode::COMMON_EXCEPTION, 403],
        ValidationException::class => [],
        UnauthorizedHttpException::class => ['未登录或登录状态失效', ExceptionCode::COMMON_EXCEPTION, 422],
        TokenInvalidException::class => ['token不正确', ExceptionCode::COMMON_EXCEPTION, 400],
        NotFoundHttpException::class => ['没有找到该页面', ExceptionCode::COMMON_EXCEPTION, 404],
        MethodNotAllowedHttpException::class => ['访问方式不正确', ExceptionCode::COMMON_EXCEPTION, 405],
        QueryException::class => ['查询语句不正确', ExceptionCode::COMMON_EXCEPTION, 400],
        ThrottleRequestsException::class => ['请求频率过快', ExceptionCode::COMMON_EXCEPTION, 429],
        InvalidRequestException::class => [],// 用户错误行为触发的异常
        ThirdPartyRequestException::class => [],// 请求第三方服务异常
        RpcRequestException::class => [],// rpc 服务异常
        WeappRequestException::class => ['用户未注册，请先注册', ExceptionCode::COMMON_EXCEPTION, 200],// 小程序登录异常
        SqlRunException::class => ['请检查您的 Sql 语句', ExceptionCode::COMMON_EXCEPTION, 400],// sql 语句执行错误
        OAuthServerException::class => ['授权系统出错', ExceptionCode::AUTH_EXCEPTION, 400],
    ];

    public function register($className, callable $callback)
    {
        $this->doReport[$className] = $callback;
    }

    /**
     * @return bool
     */
    public function shouldReturn()
    {
        //只有请求包含是json或者ajax请求时才有效
//        if (! ($this->request->wantsJson() || $this->request->ajax())){
//
//            return false;
//        }
        foreach (array_keys($this->doReport) as $report) {
            if ($this->throwable instanceof $report) {
                $this->report = $report;
                return true;
            }
        }

        return false;
    }

    /**
     * @param Throwable $e
     * @return static
     */
    public static function make(Throwable $e)
    {

        return new static(\request(), $e);
    }

    /**
     * @return mixed
     */
    public function report()
    {
        if ($this->throwable instanceof ValidationException) {
            $error = Arr::first($this->throwable->errors());
            return $this->failed(Arr::first($error), ExceptionCode::VALIDATION_EXCEPTION, $this->throwable->getCode());
        }
        if ($this->throwable instanceof AuthenticationException) {
            return $this->failed($this->throwable->getMessage(), ExceptionCode::AUTH_EXCEPTION);
        }
        if ($this->throwable instanceof InvalidRequestException) {
            return $this->failed($this->throwable->getMessage(), $this->throwable->getStatusCode(), $this->throwable->getCode());
        }
        if ($this->throwable instanceof ThirdPartyRequestException) {
            return $this->failed($this->throwable->getMessage(), ExceptionCode::THIRD_PARTY_EXCEPTION, $this->throwable->getCode());
        }
        if ($this->throwable instanceof RpcRequestException) {
            return $this->failed($this->throwable->getMessage(), ExceptionCode::RPC_EXCEPTION, $this->throwable->getCode());
        }
        if ($this->throwable instanceof OAuthServerException) {
            Log::error($this->throwable->getMessage());
        }
        $message = $this->doReport[$this->report];
        return $this->failed($message[0], $message[1], $message[2]);
    }

    public function prodReport()
    {
        return $this->failed('服务器错误', ExceptionCode::INTERNAL_EXCEPTION, 500);
    }
}
