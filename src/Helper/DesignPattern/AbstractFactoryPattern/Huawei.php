<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IUser\HuaweiUser;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IPay\HuaweiPay;
/**
 * 华为渠道
 */
class Huawei extends Channel implements IChannel{
    public  function createUser(){
        return new HuaweiUser($this->getChanneData());
    }
    public  function createPay(){
        return new HuaweiPay($this->getChanneData());
    }
}
