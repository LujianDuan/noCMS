<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\APi\AdminResource;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Admin;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;


class PassportController extends Controller
{

    const AUTHON_TOKEN_KEY = 'nocms:token:client_id_';

    protected $server;
    protected $tokens;
    protected $jwt;

    public function __construct(AuthorizationServer $server,
        TokenRepository $tokens,
        JwtParser $jwt)
    {
        $this->jwt = $jwt;
        $this->server = $server;
        $this->tokens = $tokens;
    }

    public function login(ServerRequestInterface $request){
        $body = $request->getParsedBody();
        if(!isset($body['username'])){
            return $this->failed('用户名不能为空'); 
        }
        if(!isset($body['password'])){
            return $this->failed('密码不能为空'); 
        }
        if(!isset($body['client_id'])){
            $body['client_id'] = config('app.client_id'); 
        }
        if(!isset($body['client_secret'])){
            $body['client_secret'] = config('app.client_secret'); 
        }
        if(!isset($body['grant_type'])){
            $body['grant_type'] = config('app.grant_type'); 
        }
        $user = (new Admin())->findForPassport($body['username']);
        if(!$user){
            return $this->failed('用户不存在'); 
        }

        $key = self::AUTHON_TOKEN_KEY.$body['client_id'].':user_'.$user->id;
        $token = Redis::get($key);
        if($token){
            $expireAt = Redis::TTL($key);
            $data =[
                'user'=>new AdminResource($user), 
                'permissions'=>[],
                'roles'=>[['id'=>'admin']],
                'token'=>$token,
                'token_type'=>'Bearer',
                'expireAt'=>date('Y-m-d H:i:s',$expireAt + time()),
                'msg'=>'欢迎你回来!',
            ];
            $valid = Auth::guard()->attempt(['email'=>$user['email'],'password'=>$body['password']]);
            if(!$valid){
                return $this->failed('密码错误'); 
            }
            return $this->success($data);
        }
        $request = $request->withParsedBody($body);
        $psrResponse = $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        $status = $psrResponse->getStatusCode();
        if($status!=200){
            $this->failed('登录失败'); 
        }
        $response_body = $psrResponse->getBody();
        $response = json_decode($response_body,true);
        //首次请求将response数组存入redis,过期时间设置为 expires_in-500,之后每次请求，先从redis获取，取不到再调服务取

        Redis::SETEX($key,$response['expires_in'],$response['access_token']);


        $data = [
            'user'=>new AdminResource($user), 
            'permissions'=>[['id'=>'queryForm','operation'=>['add','edit']]],
            'roles'=>[['id'=>'admin','operation'=>['add','edit','delete']]],
            'token'=>$response['access_token'],
            'token_type'=>$response['token_type'],
            'expireAt'=>date('Y-m-d H:i:s',$response['expires_in']+time()),
            'msg'=>'欢迎你回来!'
        ];
        return $this->success($data);
    }

    /**
     *
     * 返回路由让前端使用
     */
    public function routes(){
       return [];  
    }
}
