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
use Endness\Helper\RequestHelper;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Codec\Json;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SystemMiddleware implements MiddlewareInterface
{
    private const SUPER_ADMIN_LEVEL = 99; //超级管理员

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $classMethod = explode(':', RequestHelper::getAdminModule());
        $annotations = AnnotationCollector::getClassMethodAnnotation($classMethod[0], $classMethod[1]);
        $jwtData = JwtHelper::decodeWithRequest('SYSTEM', $request);
        if (! $jwtData && $annotationsMiddleware = (array) $annotations['Hyperf\HttpServer\Annotation\Middleware']) {
            if (! empty($annotationsMiddleware)) {
                $annotationsMiddleware = array_values($annotationsMiddleware);
                array_walk($annotationsMiddleware, function (&$val, $key) {$val = array_unique(array_column((array) $val, 'middleware')); });
            }
            //放行中间件配置项
            $passOtherMiddleware = ['Endness\Middleware\UserMiddleware', 'Endness\Middleware\OrgMiddleware'];
            $passAuth = array_intersect($annotationsMiddleware[0], $passOtherMiddleware);
            if (! empty($passAuth)) {
                return $handler->handle($request);
            }
            //针对单个接口继承多个服务中间件鉴权 则只校验本服务token-type的token 其他服务则放行
        }
        if (! $jwtData || time() - $jwtData->iat > 86400 * 14) { // 未登录，或登录状态超过14天
            return self::json('请登录！');
        }
        try {
            $redisClient = redis('public');
            if ($redisClient->exists('admin_status_' . $jwtData->data->id) && $redisClient->get('admin_status_' . $jwtData->data->id) == 'deleted') {
                // 用户已被删除   key= admin_status_1(uid) value = deleted
                return self::json('请登录！');
            }
        } catch (\Exception $exception) {
            logger()->error('SYSTEM REDIS CLIENT ERROR', ['module' => RequestHelper::getAdminModule()]);
        }
        try {
            $redisOrgClient = redis('org');
            if ($redisOrgClient->exists('org_user_status_' . $jwtData->data->id) && $redisOrgClient->get('org_user_status_' . $jwtData->data->id) == 'deleted') {
                return self::json('您已被移出该机构!');
            }
        } catch (\Exception $exception) {
            logger()->error('ORG REDIS CLIENT ERROR', ['module' => RequestHelper::getAdminModule()]);
        }

        $jwtData->data->tokenType = 'system';

        if ($jwtData->data->level == self::SUPER_ADMIN_LEVEL) { // 超级管理员
            contextSet('nowUser', $jwtData->data); //将登录信息存储到协程上下文
            unset($jwtData);
            return $handler->handle($request);
        }

        //不经过权限认证
        $classMethod = explode(':', RequestHelper::getAdminModule());
        $annotations = AnnotationCollector::getClassMethodAnnotation($classMethod[0], $classMethod[1]);
        if (! array_key_exists('Endness\Annotation\SystemPermission', $annotations)) {
            contextSet('nowUser', $jwtData->data); //将登录信息存储到协程上下文
            unset($jwtData);
            return $handler->handle($request);
        }

        $adminRole = $jwtData->data->role_id;
        if ($jwtData->data->id && empty($adminRole)) {
            return self::json('账号异常!未绑定角色身份', ApiHelper::AUTH_ERROR);
        }
        if (! $adminRole || ! $this->allowAccess($jwtData->data->role_id)) {
            return self::json('无权访问', ApiHelper::AUTH_ERROR);
        }
        contextSet('nowUser', $jwtData->data); //将登录信息存储到协程上下文
        unset($jwtData, $adminRole);
        return $handler->handle($request);
    }

    private static function json(string $msg, int $errCode = ApiHelper::LOGIN_ERROR)
    {
        $body = new SwooleStream(Json::encode(ApiHelper::genErrorData($msg, $errCode)));
        return Context::get(ResponseInterface::class)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody($body);
    }

    /**
     * Notes:判断路由节点访问是否拥有权限
     * User: Endness
     * Date: 2021/10/11
     * Time: 17:22.
     * @param int $role_id 当前用户角色id
     */
    private function allowAccess(int $role_id)
    {
        $authRedisKey = 'SYSTEM_RBAC_' . $role_id;
        $rbacAccess = [];
        try {
            //从redis中获取该用户最新菜单权限
            $redisClient = redis('public');
            if ($redisClient->exists($authRedisKey)) {
                $rbacAccess = Json::decode($redisClient->get($authRedisKey));
            } else {
                return self::json('请重新登录！');
            }
        } catch (\Exception $exception) {
            logger()->error('SYSTEM REDIS CLIENT ERROR', ['module' => RequestHelper::getAdminModule()]);
        }
        $adminModule = RequestHelper::getAdminModule();
        if (in_array($adminModule, $rbacAccess) && ! empty($rbacAccess)) {
            return true;
        }
        return false;
    }
}
