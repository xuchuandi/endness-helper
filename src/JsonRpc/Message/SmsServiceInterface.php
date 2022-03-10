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

interface SmsServiceInterface
{
    /**
     * 发送短信.
     * @param string $phone 手机号
     * @param string $phoneCountry 手机区号
     * @param string $alias 模板别名
     * @param array $templateParam 模板参数
     */
    public function sendSms(string $phone, string $phoneCountry, string $alias, array $templateParam): array;

    /**
     * 校验验证码.
     */
    public function checkSmsCode(string $phone, string $phoneCountry, string $code): array;
}
