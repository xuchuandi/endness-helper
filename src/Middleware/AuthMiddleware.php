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
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tokenType = strtoupper($request->getHeaderLine('token-type'));
        $jwtData = JwtHelper::decodeWithRequest($tokenType, $request);
        if (! $jwtData || time() - $jwtData->iat > 86400 * 14) { // 未登录，或登录状态超过14天
            return self::json('请登录！');
        }

        $jwtData->data->tokenType = $tokenType;

        contextSet('nowUser', $jwtData->data); //将登录信息存储到协程上下文
        return $handler->handle($request);
    }

    private static function json(string $msg)
    {
        $body = new SwooleStream(Json::encode(ApiHelper::genErrorData($msg, ApiHelper::LOGIN_ERROR)));
        return Context::get(ResponseInterface::class)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody($body);
    }
}
