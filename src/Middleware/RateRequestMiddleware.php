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
use Endness\Helper\RequestHelper;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RateRequestMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $classMethod = explode(':', RequestHelper::getAdminModule());
        $handleArr = request()->all();

        //如果为空，认定为raw请求
        if (! $handleArr) {
            $handleArr = [
                'body' => request()->getBody()->getContents(),
            ];
        }

        $handleArr['class'] = $classMethod[0];
        $handleArr['method'] = $classMethod[1];

        $redis = redis();
        $strKey = 'rate_request:' . md5(serialize($handleArr));
        //如果锁存在
        $info = $redis->get($strKey);
        if ($info) {
            return self::json('请求正在执行，请稍后再试');
        }

        $redis->set($strKey, Json::encode($handleArr), 6);

        //写进协程，由业务自行删除
        contextSet('rateRequestKey', $strKey);

        return $handler->handle($request);
    }

    private static function json(string $msg, int $errCode = ApiHelper::NORMAL_ERROR)
    {
        $body = new SwooleStream(Json::encode(ApiHelper::genErrorData($msg, $errCode)));
        return Context::get(ResponseInterface::class)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody($body);
    }
}
