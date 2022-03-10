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
namespace Endness\JsonRpc\Publics;

use Endness\Helper\ApiHelper;
use Hyperf\RpcClient\AbstractServiceClient;

class SystemMenuServiceConsumer extends AbstractServiceClient implements SystemMenuServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     * @var string
     */
    protected $serviceName = 'SystemMenuService';

    /**
     * 定义对应服务提供者的服务协议.
     * @var string
     */
    protected $protocol = 'jsonrpc-http';

    /**
     * 添加菜单.
     */
    public function addMenu(array $menuData): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('menuData'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
