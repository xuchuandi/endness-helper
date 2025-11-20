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
namespace Endness\JsonRpc\Third;

use Endness\Helper\ApiHelper;
use Hyperf\RpcClient\AbstractServiceClient;

class ThirdServiceConsumer extends AbstractServiceClient implements ThirdServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     * @var string
     */
    protected string $serviceName = 'ThirdService';

    /**
     * 定义对应服务提供者的服务协议.
     * @var string
     */
    protected string $protocol = 'jsonrpc-http';

    /**
     * 通过指定条件获取第三方授权用户信息.
     */
    public function getThirdUser(array $where): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('where'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 生成微信公众号授权URL.
     */
    public function buildWechatAuthUrl(string $wechatAppid, string $callbackUrl): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('wechatAppid', 'callbackUrl'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
