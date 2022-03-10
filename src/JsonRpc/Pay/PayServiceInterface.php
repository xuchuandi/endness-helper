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

interface PayServiceInterface
{
    /**
     * 下单.
     */
    public function addOrder(array $orderData): array;

    /**
     * 获取订单.
     * @param string $business 业务类型
     * @param int $businessId 业务订单ID
     */
    public function getOrder(string $business, int $businessId): array;
}
