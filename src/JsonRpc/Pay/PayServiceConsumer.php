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
namespace Endness\JsonRpc\Pay;

use Endness\Helper\ApiHelper;
use Hyperf\RpcClient\AbstractServiceClient;

class PayServiceConsumer extends AbstractServiceClient implements PayServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     * @var string
     */
    protected string $serviceName = 'PayService';

    /**
     * 定义对应服务提供者的服务协议.
     * @var string
     */
    protected string $protocol = 'jsonrpc-http';

    /**
     * 下单.
     */
    public function addOrder(array $orderData): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('orderData'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 获取订单.
     * @param string $business 业务类型
     * @param int $businessId 业务订单ID
     */
    public function getOrder(string $business, int $businessId): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('business', 'businessId'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
