<?php
namespace Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module\IUser;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\Module;
use Endness\Helper\DesignPattern\AbstractFactoryPattern\AbstractFactory\IUserModule;
class HuaweiUser extends Module implements IUserModule{

    public function login($data)
    {
        // TODO: Implement login() method.
        echo "huawei Login success.".PHP_EOL;
        return "";
    }
}
