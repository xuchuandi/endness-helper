<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IPay;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\Module;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IPayModule;
class HuaweiPay extends Module implements IPayModule
{
    public function order()
    {
        echo "huawei generate order.".PHP_EOL;

        return true;
    }

    public function payCallback(){
        echo "huawei pay callback success.".PHP_EOL;
        return true;
    }
}
