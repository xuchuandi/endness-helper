<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory;
/**
 * 模块 父类
 */
class Module{
    protected $channeObj = null;
    public function __construct($channelObj)
    {
        $this->channeObj = $channelObj;
    }
}
