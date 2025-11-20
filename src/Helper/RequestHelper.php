<?php
/**
 * 请求相关.
 */
declare(strict_types=1);
/**
 * This file is part of Endness.
 *
 * @link     https://www.yunEndness.cn
 * @document https://www.yunEndness.cn/document/
 * @contact  www.yunEndness.cn 7*12 9:00-21:00
 * @license  https://www.yunEndness.cn/LICENSE
 */
namespace Endness\Helper;

use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Redis\RedisFactory;
use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Psr\Http\Message\ServerRequestInterface;

class RequestHelper
{
    /**
     * 获取客户端IP.
     */
    public static function getClientIp(?ServerRequestInterface $request = null): string
    {
        if (! $request) {
            $request = Context::get(ServerRequestInterface::class);
        }
        if (! $request) {
            return '';
        }
        $ip = $request->getHeaderLine('x-real-ip');
        if (! empty($ip)) {
            return $ip;
        }
        $params = $request->getServerParams();
        return $params['remote_addr'] ?? '';
    }

    /**
     * 获取当前使用域名.
     */
    public static function getClientDomain(?ServerRequestInterface $request = null): string
    {
        if (! $request) {
            $request = Context::get(ServerRequestInterface::class);
        }
        if (! $request) {
            return '';
        }
        $host = $request->getHeaderLine('host');
        if (! empty($host)) {
            return $host;
        }
        return '';
    }

    public static function isAjax(?ServerRequestInterface $request = null): bool
    {
        if (! $request) {
            $request = Context::get(ServerRequestInterface::class);
        }
        if (! $request) {
            return false;
        }
        return $request->getHeaderLine('x-requested-with') === 'XMLHttpRequest';
    }

    public static function getAdminModule(?ServerRequestInterface $request = null): ?string
    {
        $request || $request = Context::get(ServerRequestInterface::class);
        if (! $request) {
            return null;
        }
        $classAndMethod = self::getClassAndMethod($request);
        if (! $classAndMethod) {
            return null;
        }
        return $classAndMethod[0] . ':' . $classAndMethod[1];
    }

    public static function getClassAndMethod(?ServerRequestInterface $request = null): ?array
    {
        $request || $request = Context::get(ServerRequestInterface::class);
        if (! $request) {
            return null;
        }
        $dispatched = $request->getAttribute(Dispatched::class);
        if ($dispatched instanceof Dispatched) {
            $callback = ($dispatched->handler->callback ?? '');
            if (is_string($callback)) {
                if (strpos($callback, '@') !== false) {
                    return explode('@', $callback);
                }
                return explode('::', $callback);
            }
            if (is_array($callback) && isset($callback[0], $callback[1])) {
                return $callback;
            }
            return null;
        }
        return null;
    }

    /**
     * 使用“令牌桶”算法实现限流,如果返回False,表示需要限制!
     */
    public static function rateLimit(string $strId, int $intNum, int $intSec, string $strRedisPool = 'rate_limit'): bool
    {
        $strId = trim($strId);
        if (! strlen($strId)) {
            throw new \Exception('标识不能为空字符', ApiHelper::CODE_ERROR);
        }
        if ($intNum <= 0 || $intSec <= 0) {
            throw new \Exception('限定的数量或时间，必须是大于0的整数', ApiHelper::CODE_ERROR);
        }
        $objRedis = ApplicationContext::getContainer()->get(RedisFactory::class)->get($strRedisPool);
        if (! $objRedis) {
            throw new \Exception('获取Redis连接失败', ApiHelper::CODE_ERROR);
        }
        $strKey = 'rate_limit:' . md5($strId);
        $objRedis->watch($strKey);
        $arrData = $objRedis->hGetAll($strKey);
        $arrData || $arrData = [];
        $floN = $arrData['n'] ?? 0;
        $intNow = time();
        $blnRe = true;
        if ($arrData) {
            $intT = $arrData['t'] ?? ($intNow - 1);
            $floN += ($intNum / $intSec) * ($intNow - $intT) - 1;
            $floN = min($intNum, $floN);
            $blnRe = ($floN >= 0);
        } else {
            $floN = $intNum - 1;
        }
        if ($blnRe) {
            $arrData = ['n' => $floN, 't' => $intNow];
            $objRedis->multi();
            $objRedis->hMset($strKey, $arrData);
            $objRedis->expire($strKey, $intSec);
            $arrTemp = $objRedis->exec();
            $blnRe = (is_array($arrTemp) && count($arrTemp));
        } else {
            $objRedis->unwatch();
        }
        return $blnRe;
    }

    /**
     * 取客户唯一ID.
     */
    public static function getGuestUid(?ServerRequestInterface $request = null): ?string
    {
        $uid = null;
        $jwt = JwtHelper::decodeWithRequest(JwtHelper::GUEST_JWT_TOKEN, $request);
        if ($jwt) {
            $uid = JwtHelper::dataToHash($jwt);
        }
        return $uid;
    }
}
