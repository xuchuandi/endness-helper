<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Channel;

/**
 * 渠道 父类
 */
abstract class Channel{
    private $_channelData = [];
    private $_channeName = 'xiaomi';
    public function __construct($channelData)
    {
        $this->_channelData = $channelData;
        $this->_channeName = $channelData['name'];
    }
    public function getChanneData(){
        return $this->_channelData;
    }
    public function getChannelName(){
        return $this->_channeName;
    }
}
