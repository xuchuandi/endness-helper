<?php
namespace Endness\Helper\DesignPattern\StrategyPattern;

use Endness\Helper\DesignPattern\StrategyPattern\Strategy\Discount5;
use Endness\Helper\DesignPattern\StrategyPattern\Strategy\Discount7;
use Endness\Helper\DesignPattern\StrategyPattern\Strategy\Discount9;

class DiscountFactory{

    private $_discount = null;

    public function __construct($discountInt)
    {
        switch ($discountInt){
            case 5:
                $this->_discount = new Discount5();
                break;
            case 7:
                $this->_discount = new Discount7();
                break;
            case 9:
                $this->_discount = new Discount9();
                break;
            default:
                return null;
        }
    }

    public function count($price){
        return $this->_discount->count($price);
    }
}
