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

class SmsServiceConsumer extends AbstractServiceClient implements SmsServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     * @var string
     */
    protected string $serviceName = 'SmsService';

    /**
     * 定义对应服务提供者的服务协议.
     * @var string
     */
    protected string $protocol = 'jsonrpc-http';

    public function sendSms(string $phone, string $phoneCountry, string $alias, array $templateParam): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('phone', 'phoneCountry', 'alias', 'templateParam'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    public function checkSmsCode(string $phone, string $phoneCountry, string $code): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('phone', 'phoneCountry', 'code'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
