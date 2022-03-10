<?php
namespace Endness\Helper\DesignPattern\ProxyPattern\Proxy;

class Proxy implements Sourceable{
    /**
     * @var Source
     */
    private $_source;

    public function __construct()
    {
        //代理和装饰的区别，代理一开始就知道代理谁，不需要客户端传递代理对象
        $this->_source = new Source();
    }

    public function method()
    {
        // TODO: Implement method() method.
        $this->_source->method();
    }

    private function after(){
        print_r("代理尾巴...\n");
    }

    private function before(){
        print_r("代理开始...\n");
    }
}
