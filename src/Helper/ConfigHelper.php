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
namespace Endness\Helper;

class ConfigHelper
{
    /**
     * 按名称获取配置项值.
     * @param string $name 配置项的名称
     */
    public static function getConfig(string $name)
    {
        $configArr = config('systemConfig');

        //若存在值，则返回
        if (isset($configArr[$name])) {
            return $configArr[$name];
        }

        return null;
    }
}
