<?php
namespace Endness\Helper\DesignPattern\ProxyPattern\Proxy;

class Source implements Sourceable{
    public function method()
    {
        // TODO: Implement method() method.
        print_r("原类原方法\n");
    }
}
