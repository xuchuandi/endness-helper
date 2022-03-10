<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IPay;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\IPayModule;
/**
 * 小米渠道支付模块
 */
class XiaomiPay extends Module implements IPayModule{
    public function order()
    {
        echo "xiaomi generate order.".PHP_EOL;
        return true;
    }

    public function payCallback(){
        echo "xiaomi pay callback success.".PHP_EOL;
        return true;
    }
}
