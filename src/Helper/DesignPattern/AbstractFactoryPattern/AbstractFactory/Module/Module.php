<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module;
/**
 * 模块 父类
 */
abstract class Module{
    protected $channeObj = null;
    public function __construct($channelObj)
    {
        $this->channeObj = $channelObj;
    }
}
