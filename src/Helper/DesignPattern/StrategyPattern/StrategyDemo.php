<?php

namespace Endness\Helper\DesignPattern\StrategyPattern;

class StrategyDemo
{
    //put your code here
    public static function main()
    {
        $price = 100;
//        $discount = new Discount7();//由外部客户端选择策略，不论什么策略都提供最终计算价格
        $discount = new DiscountFactory(7);//结合工厂
        echo "原价：".$price."，最终价格：".$discount->count($price)."\n";

    }

    //call StrategyDemo::main(); share your hands


}
