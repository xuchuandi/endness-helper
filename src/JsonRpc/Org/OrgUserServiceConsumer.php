<?php

declare(strict_types=1);
/**
 * This file is part of endness Bailing.
 *
 * @link     https://www.endnessai.com
 * @document https://help.endnessai.com
 * @contact  www.endnessai.com 7*12 9:00-21:00
 */
namespace Endness\JsonRpc\Org;

use Endness\Helper\ApiHelper;
use Hyperf\RpcClient\AbstractServiceClient;

class OrgUserServiceConsumer extends AbstractServiceClient implements OrgUserServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     */
    protected string $serviceName = 'OrgUserService';

    /**
     * 定义对应服务提供者的服务协议.
     */
    protected string $protocol = 'jsonrpc-http';

    public function call(string $method, array $param): array
    {
        try {
            if (! $method) {
                throw new \Exception('method不存在，请传参');
            }
            return $this->__request(__FUNCTION__, compact('method', 'param'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
