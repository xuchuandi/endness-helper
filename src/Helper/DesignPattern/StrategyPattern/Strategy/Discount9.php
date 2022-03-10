<?php
namespace Endness\Helper\DesignPattern\StrategyPattern\Strategy;

class Discount9 implements IDiscount{
    public function count($price)
    {
        // TODO: Implement count() method.
        return $price * 0.9;
    }
}
