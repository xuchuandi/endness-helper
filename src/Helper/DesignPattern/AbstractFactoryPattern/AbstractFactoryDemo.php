<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel\ChannelFactory;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\ModuleFactory;
class AbstractFactoryDemo
{
    /**
     * 支付入口方法
     */
    public static function main(){
        $channelData = [
            "name"=>"huawei",
            "appid"=>"xxxx"
        ];
        //方式1
        $channelObj = ChannelFactory::createChannel($channelData);
        $userModule = $channelObj->createUser();
        $userModule->login([]);
        $payModule = $channelObj->createPay();
        $payModule->order();
        $payModule->payCallback();

//        //方式2
//        $channelObj = new Channel($channelData);
//        $userModule = ModuleFactory::createUserModule($channelObj);
//        $userModule->login([]);
//        $payModule = ModuleFactory::createPayModule($channelObj);
//        $payModule->order();
//        $payModule->payCallback();
    }

    //call AbstractFactoryDemo::main(); share your hands
}