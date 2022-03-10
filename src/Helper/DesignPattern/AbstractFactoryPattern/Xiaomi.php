<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel;
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