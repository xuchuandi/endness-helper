<?php
namespace Endness\Helper\DesignPattern\StrategyPattern\Strategy;

class Discount5 implements IDiscount{
    public function count($price)
    {
        // TODO: Implement count() method.
        return $price * 0.5;
    }
}
