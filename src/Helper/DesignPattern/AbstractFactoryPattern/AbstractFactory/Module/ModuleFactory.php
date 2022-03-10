<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module;

class ModuleFactory{
    public static function createUserModule($channelObj){
        $className = "Endness\\DesignPattern\\AbstractFactoryPattern\\".ucfirst($channelObj->getChannelName())."User";
        return new $className($channelObj);
    }
    public static function createPayModule($channelObj){
        $className = "Endness\\DesignPattern\\AbstractFactoryPattern\\".ucfirst($channelObj->getChannelName())."Pay";
        return new $className($channelObj);
    }
}
