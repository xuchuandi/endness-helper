<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IUser;

use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\Module;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IUserModule;
/**
 * 小米渠道用户模块
 */
class XiaomiUser extends Module implements IUserModule{
    public function login($data){
        echo "xiaomi Login success.".PHP_EOL;
        return "";
    }
}