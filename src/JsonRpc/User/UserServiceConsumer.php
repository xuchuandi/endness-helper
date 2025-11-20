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
namespace Endness\JsonRpc\User;

use Endness\Helper\ApiHelper;
use Hyperf\RpcClient\AbstractServiceClient;

class UserServiceConsumer extends AbstractServiceClient implements UserServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     * @var string
     */
    protected string $serviceName = 'UserService';

    /**
     * 定义对应服务提供者的服务协议.
     * @var string
     */
    protected string $protocol = 'jsonrpc-http';

    /**
     * 通过手机号找用户.
     */
    public function getUserByPhone(string $phone, int $phoneCountry = 86): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('phone', 'phoneCountry'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 通过uid集合找用户.
     */
    public function getUserByUids(array $uidArr): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('uidArr'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 通过uid和appid找对应的用户第三方授权信息.
     */
    public function getUserThirdByUidAndAppid(int $uid, string $appid): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('uid', 'appid'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 绑定用户第三方信息.
     */
    public function addUserThird(int $user_id, string|null $appid, array $userInfo): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('user_id', 'appid', 'userInfo'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
