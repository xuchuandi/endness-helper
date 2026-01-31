<?php

declare(strict_types=1);
/**
 * This file is part of Endness.
 *
 * @link     https://www.yunEndness.cn
 * @document https://www.yunEndness.cn/document/
 * @contact  www.yunEndness.cn 7*12 9:00-21:00
 * @license  https://www.yunEndness.cn/LICENSE
 */
namespace Endness\Middleware;

use Endness\Helper\ApiHelper;
use Endness\Helper\JwtHelper;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Codec\Json;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $jwtData = JwtHelper::decodeWithRequest('USER', $request);

        // 如果其他中间件有存储用户信息到上下文，则证明其他中间件有校验用户身份，直接放行。若需要两个中间件，user中间件放后面。
        if (! $jwtData && contextGet('nowUser')) {
            return $handler->handle($request);
        }

        // 未登录，或登录状态超过14天
        if (! $jwtData || time() - $jwtData->iat > 86400 * 14) {
            return self::json('请登录！');
        }

        $jwtData->data->tokenType = 'user';

        if (!empty($jwtData->data->expire_unix) && $jwtData->data->expire_unix < time()) {
            return self::json('用户权益已到期', ApiHelper::AUTH_ERROR);
        }

        //将登录信息存储到协程上下文
        contextSet('nowUser', $jwtData->data);
        return $handler->handle($request);
    }

    private static function json(string $msg, int $errCode = ApiHelper::LOGIN_ERROR)
    {
        $body = new SwooleStream(Json::encode(ApiHelper::genErrorData($msg, $errCode)));
        return Context::get(ResponseInterface::class)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody($body);
    }
}
