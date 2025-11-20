<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern;

use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel\Channel;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel\IChannel;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IUser\XiaomiUser;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IPay\XiaomiPay;
/**
 * 小米渠道
 */
class Xiaomi extends Channel implements IChannel{
    public  function createUser(){
        return new XiaomiUser($this->getChanneData());
    }
    public  function createPay(){
        return new XiaomiPay($this->getChanneData());
    }
}