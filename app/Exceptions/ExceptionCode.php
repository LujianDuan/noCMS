<?php


namespace App\Exceptions;

/**
 * 自定义业务异常类
 * Class ExceptionCode
 * @package App\Exceptions
 */
class ExceptionCode
{
    const HTTP_OK_BUT_FAIL = 1001;// 请求已收到，但处理失败
    const HTTP_OK = 200;// 虽然捕获异常，但是需要正常状态码返回数据

    const JOIN_GROUP_FAIL = 2001;// 参与拼团失败，团满了或团的有效期到了
    const JOIN_GROUP_END = 2002;// 参与拼团失败，此拼团活动结束了
    const NO_BIND_TEL = 2003;// 用户未绑定手机号
    const IS_NOT_VIP = 2004;// 用户未申请尊享会员

    const COMMON_EXCEPTION = 4000;// 普通异常
    const AUTH_EXCEPTION = 4001;// 权限异常
    const VALIDATION_EXCEPTION = 4002;// 数据校验异常
    const THIRD_PARTY_EXCEPTION = 4003;// 第三方服务异常
    const RPC_EXCEPTION = 4004;// rpc 服务异常

    const INTERNAL_EXCEPTION = 5000;// 系统异常

    public static $codeText = [
        '4000' => '普通异常',
    ];
}
