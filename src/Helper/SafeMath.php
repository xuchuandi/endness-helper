<?php

declare(strict_types=1);
/**
 * This file is part of Kuaijing Bailing.
 *
 * @link     https://www.kuaijingai.com
 * @document https://help.kuaijingai.com
 * @contact  www.kuaijingai.com 7*12 9:00-21:00
 */
namespace Endness\Helper;

class SafeMath
{
    /**
     * 加法.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function add($self, $other, $scale = 18)
    {
        $self = self::format($self, $scale);
        $other = self::format($other, $scale);
        return bcadd((string) $self, (string) $other, $scale);
    }

    /**
     * 减法.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function sub($self, $other, $scale = 18)
    {
        $self = self::format($self, $scale);
        $other = self::format($other, $scale);
        return bcsub((string) $self, (string) $other, $scale);
    }

    /**
     * 乘法.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function mul($self, $other, $scale = 18)
    {
        $self = self::format($self, $scale);
        $other = self::format($other, $scale);
        return bcmul((string) $self, (string) $other, $scale);
    }

    /**
     * 除法.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function div($self, $other, $scale = 18)
    {
        $self = self::format($self, $scale);
        $other = self::format($other, $scale);
        return (float) $other ? bcdiv((string) $self, (string) $other, $scale) : 0;
    }

    /**
     * 乘方.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function pow($self, $other, $scale = 18)
    {
        $self = self::format($self, $scale);
        $other = self::format($other, $scale);
        return bcpow((string) $self, (string) $other, $scale);
    }

    /**
     * 大于.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function gt($self, $other, $scale = 18)
    {
        return self::cmp($self, $other, $scale) > 0;
    }

    /**
     * 大于等于.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function gte($self, $other, $scale = 18)
    {
        return self::cmp($self, $other, $scale) >= 0;
    }

    /**
     * 小于.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function lt($self, $other, $scale = 18)
    {
        return self::cmp($self, $other, $scale) < 0;
    }

    /**
     * 小于等于.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function lte($self, $other, $scale = 18)
    {
        return self::cmp($self, $other, $scale) <= 0;
    }

    /**
     * 比较.
     * @param mixed $self
     * @param mixed $other
     * @param mixed $scale
     */
    public static function cmp($self, $other, $scale = 18)
    {
        $self = self::format($self, $scale);
        $other = self::format($other, $scale);
        return bccomp((string) $self, (string) $other, $scale);
    }

    /**
     * 格式化数据，如果是科学计数法则进行转换.
     * @param mixed $value
     * @param mixed $scale
     */
    public static function format($value, $scale)
    {
        $split = preg_split('/(e[+-])|e/i', strval($value), 0);

        switch (count($split)) {
            case 1:
                return $value;
            case 2:
                $base = $split[0];
                $times = self::pow(10, $split[1], $scale);
                return stripos(strval($value), '-')
                    ? self::div($base, $times, $scale)
                    : self::mul($base, $times, $scale);
            default:
                throw new \Exception("Invalid Argument {$value}", 1);
        }
    }

    /**
     * Notes: 金额格式化
     * User: Endness
     * Date: 2021/11/16
     * Time: 10:25.
     * @param int $precision 保留小数点后面的位数
     * @param mixed $price
     * @return string
     */
    public static function priceFormat($price, $precision = 2)
    {
        return is_int($price) ? intval($price) : floatval(sprintf('%.' . $precision . 'f', round(floatval($price), $precision)));
    }

    /**
     * Notes: 封装累加
     * Author: Endness
     * Date: 2023/3/10 14:39.
     * @param mixed $num1
     * @param mixed $num2
     * @return null|mixed|string
     */
    public static function jia($num1, $num2, ...$num3)
    {
        $arg_list = func_get_args(); // 参数array
        $numargs = func_num_args(); // 参数数量
        if ($numargs == 0) {
            return null;
        }
        if ($numargs == 1) {
            return $arg_list[0];
        }
        $res = $arg_list[0];
        for ($i = 1; $i < $numargs; ++$i) {
            $res = SafeMath::add((string) $res, (string) $arg_list[$i], 2);
        }
        return $res;
    }
}
