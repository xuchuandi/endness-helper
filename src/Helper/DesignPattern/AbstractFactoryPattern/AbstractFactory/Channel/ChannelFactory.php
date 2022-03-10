<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel;

class ChannelFactory{
    public static function createChannel($channelData){
        $className = "Endness\\DesignPattern\\AbstractFactoryPattern\\".ucfirst($channelData['name']);
        return new $className($channelData);
    }
}
