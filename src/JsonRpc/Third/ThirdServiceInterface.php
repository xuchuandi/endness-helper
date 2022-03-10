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

interface ThirdServiceInterface
{
    /**
     * 通过指定条件获取第三方授权用户信息.
     */
    public function getThirdUser(array $where): array;

    /**
     * 生成微信公众号授权URL.
     */
    public function buildWechatAuthUrl(string $wechatAppid, string $callbackUrl): array;
}
