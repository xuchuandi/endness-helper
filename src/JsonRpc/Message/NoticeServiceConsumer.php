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
namespace Endness\JsonRpc\Message;

use Endness\Helper\ApiHelper;
use Hyperf\RpcClient\AbstractServiceClient;

class NoticeServiceConsumer extends AbstractServiceClient implements NoticeServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     * @var string
     */
    protected string $serviceName = 'NoticeService';

    /**
     * 定义对应服务提供者的服务协议.
     * @var string
     */
    protected string $protocol = 'jsonrpc-http';

    /**
     * 添加机构消息.
     */
    public function addOrgNotice(int $org_id, int $uid, string $title, string $content): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('org_id', 'uid', 'title', 'content'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
