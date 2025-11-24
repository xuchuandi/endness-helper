<?php

declare(strict_types=1);
/**
 * This file is part of endness Bailing.
 *
 * @link     https://www.endnessai.com
 * @document https://help.endnessai.com
 * @contact  www.endnessai.com 7*12 9:00-21:00
 */
namespace Endness\Helper;

use Hyperf\Codec\Json;
use Hyperf\Nacos\Application;
use Hyperf\Nacos\Config;
use function Hyperf\Support\env;


class NacosHelper
{
    public array $config = [];

    public Application $application;

    public function __construct()
    {
        $this->config['nacosIp'] = env('NACOS_IP', env('nacosIp', ''));
        $this->config['nacosPort'] = env('NACOS_PORT', env('nacosPort', ''));
        $this->config['nacosUser'] = env('NACOS_USER', env('nacosUser', ''));
        $this->config['nacosPwd'] = env('NACOS_PWD', env('nacosPwd', ''));
        $this->config['nacosGroupName'] = env('NACOS_GROUP_NAME', env('nacosGroupName', ''));
        $this->config['nacosNamespaceId'] = env('NACOS_NAME_SPACE_ID', env('nacosNamespaceId', ''));

        $this->application = new Application(new Config([
            'base_uri' => sprintf('http://%s:%d', $this->config['nacosIp'], $this->config['nacosPort']),
            'username' => $this->config['nacosUser'],
            'password' => $this->config['nacosPwd'],
            'guzzle_config' => [
                'headers' => [
                    'charset' => 'UTF-8',
                ],
            ],
        ]));
    }

    public function set($key, $value, $type = 'string')
    {
        $this->application->config->set($key, $this->config['nacosGroupName'], $value, $type, $this->config['nacosNamespaceId']);
    }

    public function get($key)
    {
        $response = $this->application->config->get($key, $this->config['nacosGroupName']);
        return Json::decode((string) $response->getBody());
    }
}
