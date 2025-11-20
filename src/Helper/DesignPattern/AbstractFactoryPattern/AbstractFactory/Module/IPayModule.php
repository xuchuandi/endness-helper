<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module;
/**
 * 支付模块接口
 */
interface IPayModule{
    public function order();
    public function payCallback();
}
