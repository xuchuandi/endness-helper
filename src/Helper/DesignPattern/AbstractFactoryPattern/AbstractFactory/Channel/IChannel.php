<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel;
interface IChannel{
    public function createUser();
    public function createPay();
}
