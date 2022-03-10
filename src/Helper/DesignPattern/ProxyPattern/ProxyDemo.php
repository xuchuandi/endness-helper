<?php
namespace Endness\Helper\DesignPattern\ProxyPattern;

use Endness\Helper\DesignPattern\ProxyPattern\Proxy\Proxy;

class ProxyDemo
{
    //put your code here
    public static function main(){
        (new Proxy())->method();
    }

    //call ProxyDemo::main();
}
