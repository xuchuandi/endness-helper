<?php

declare(strict_types=1);
/**
 * This file is part of endness Bailing.
 *
 * @link     https://www.endnessai.com
 * @document https://help.endnessai.com
 * @contact  www.endnessai.com 7*12 9:00-21:00
 */
namespace Endness\Helper;


class RateRequestHelper
{
    /**
     * 手动删除访问令牌的缓存.
     */
    public static function clear(): bool
    {
        $redis = redis();
        try {
            $rateRequestKey = contextGet('rateRequestKey');
            if ($rateRequestKey) {
                $redis->del($rateRequestKey);
            }
        } catch (\Exception $e) {
            stdLog()->error('RateRequestHelper redis del：' . $e->getMessage());
            return false;
        }
        return true;
    }
}
