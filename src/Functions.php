<?php

declare(strict_types=1);
/**
 * This file is part of Endness.
 *
 * @link     https://www.yunEndness.cn
 * @document https://www.yunEndness.cn/document/
 * @contact  www.yunEndness.cn 7*12 9:00-21:00
 * @license  https://www.yunEndness.cn/LICENSE
 */
use Endness\Helper\ConfigHelper;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\RedisFactory;
use Hyperf\Server\ServerFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Context;

if (! function_exists('container')) {
    /**
     * 容器实例.
     * @return \Psr\Container\ContainerInterface
     */
    function container()
    {
        return ApplicationContext::getContainer();
    }
}

if (! function_exists('redis')) {
    /**
     * redis 客户端实例.
     * @param mixed $poolName
     * @return \Hyperf\Redis\Redis|mixed
     */
    function redis($poolName = '')
    {
        if ($poolName) {
            return container()->get(RedisFactory::class)->get($poolName);
        }
        return container()->get(Hyperf\Redis\Redis::class);
    }
}

if (! function_exists('server')) {
    /**
     * server 实例 基于 swoole server.
     * @return \Swoole\Coroutine\Server|\Swoole\Server
     */
    function server()
    {
        return container()->get(ServerFactory::class)->getServer()->getServer();
    }
}

if (! function_exists('cache')) {
    /**
     * 缓存实例 简单的缓存.
     * @return mixed|\Psr\SimpleCache\CacheInterface
     */
    function cache()
    {
        return container()->get(Psr\SimpleCache\CacheInterface::class);
    }
}

if (! function_exists('stdLog')) {
    /**
     * 向控制台输出日志.
     * @return mixed|StdoutLoggerInterface
     */
    function stdLog()
    {
        return container()->get(StdoutLoggerInterface::class);
    }
}

if (! function_exists('logger')) {
    /**
     * 向日志文件记录日志.
     * @return \Psr\Log\LoggerInterface
     */
    function logger()
    {
        return container()->get(LoggerFactory::class)->make();
    }
}

if (! function_exists('request')) {
    /**
     * 请求对象
     * @param null|mixed $class
     * @return mixed|RequestInterface
     */
    function request($class = null)
    {
        if (! $class) {
            $class = RequestInterface::class;
        }
        return container()->get($class);
    }
}

if (! function_exists('cfg')) {
    /**
     * 获取数据库中的配置值.
     * @param string $name 配置项的名称
     * @return mixed|string
     */
    function cfg(string $name): ?string
    {
        return ConfigHelper::getConfig($name);
    }
}

if (! function_exists('contextSet')) {
    /**
     * 储存一个值到当前协程的上下文.
     * @param string $name key
     * @param string $value value
     */
    function contextSet(string $name, $value): mixed
    {
        return Context::set($name, $value);
    }
}

if (! function_exists('contextGet')) {
    /**
     * 从当前协程的上下文中取出一个以 $id 为 key 储存的值.
     * @param string $name key
     */
    function contextGet(string $name): mixed
    {
        return Context::get($name);
    }
}

if (! function_exists('formatTime')) {
    /**
     * 格式化展示时间，方便全球不同的展示形式，后续再处理其他国家的.
     * @param ?string $unixTime 时间
     */
    function formatTime(?string $unixTime): mixed
    {
        if (is_null($unixTime)) {
            return null;
        }
        return $unixTime;
    }
}

if (! function_exists('fileDomain')) {
    /**
     * 补全cos文件的访问路径.
     * @param null|string $filePath 文件路径
     */
    function fileDomain(?string $filePath): string
    {
        if (empty($filePath)) {
            return '';
        }

        if (substr($filePath, 0, 4) === 'http') {
            return $filePath;
        }

        $domain = 'https://' . cfg('filesystem_qcloud_bucket') . '-' . cfg('qcloud_appid') . '.cos.' . cfg('filesystem_qcloud_region') . '.myqcloud.com/';
        return $domain . $filePath;
    }
}

if (! function_exists('trimArr')) {
    /**
     * 去除数组里的字符串类型的特定下标字段的空格.
     * @param array $stringArr 字符串数组
     * @param array $filterArr 要去除的下标字段，不传为所有字符串类型都去除
     */
    function trimArr(array $stringArr, array $filterArr = []): array
    {
        // 数据为空
        if (empty($filterArr)) {
            foreach ($stringArr as &$item) {
                is_string($item) && $item = trim($item);
            }
            return $stringArr;
        }

        // 循环下标字段
        foreach ($filterArr as &$filterItem) {
            if (isset($stringArr[$filterItem]) && is_string($stringArr[$filterItem])) {
                $stringArr[$filterItem] = trim($stringArr[$filterItem]);
            }
        }
        return $stringArr;
    }
}

if (! function_exists('getMillisecond')) {
    /**
     * 获取当前毫秒.
     * @return float
     */
    function getMillisecond()
    {
        [$msec, $sec] = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
}

if (! function_exists('relativePath')) {
    /**
     * Notes: 转换业务前端表单提交的图片路径 公共方法业务表统一化存储相对路径  //fileDomain 补全文件路径
     * User: Endness
     * Date: 2021/10/26
     * Time: 10:10.
     * @param string $filePath 文件路径
     */
    function relativePath($filePath)
    {
        $domainSec = [
            'https://' . cfg('filesystem_qcloud_bucket') . '-' . cfg('qcloud_appid') . '.cos.' . cfg('filesystem_qcloud_region') . '.myqcloud.com',
        ];

        if (is_string($filePath)) {
            return ltrim(str_replace($domainSec, '', $filePath), '/');
        }

        // count(xxx) = count(xxx, 1); 1是计算多维数组中的所有元素，避免传过来的是多维数组。
        if (is_array($filePath) && count($filePath) == count($filePath, 1)) {
            foreach ($filePath as $k => $path) {
                $filePath[$k] = ltrim(str_replace($domainSec, '', $path), '/');
            }
            return $filePath;
        }
        return $filePath;
    }
}

if (! function_exists('getFormatNumber')) {
    /**
     * 获取格式化的数字格式.
     * @param $number
     * @return array|string|string[]
     */
    function getFormatNumber(string|int|float|null $number): string
    {
        $number = number_format(floatval($number), 2);
        if (strpos($number, '.') !== false) {
            $number = rtrim($number, '0');
            $number = rtrim($number, '0');
            $number = rtrim($number, '.');
        }
        $number = str_replace(',', '', $number);

        if ($number === '') {
            $number = '0';
        }

        return $number;
    }
}

if (! function_exists('gzEncodeData')) {
    /**
     * Notes: 压缩长文本数据
     * User: Endness
     * Date: 2021/12/7
     * Time: 11:15.
     * @param $data
     */
    function gzEncodeData(string $data): string
    {
        return base64_encode(gzdeflate($data, 9));
    }
}

if (! function_exists('gzDecodeData')) {
    /**
     * Notes: 解压长文本数据
     * User: Endness
     * Date: 2021/12/7
     * Time: 11:54.
     */
    function gzDecodeData(string $data): string
    {
        return gzinflate(base64_decode($data));
    }
}

if (! function_exists('getTime')) {
    /**
     * 获取能直接存入数据库 datetime 格式的时间.
     * @param int $plusSecond 当前时间增加的秒数，减传负数
     */
    function getTime($plusSecond = 0): string
    {
        return date('Y-m-d H:i:s', time() + $plusSecond);
    }
}
if (! function_exists('arrayColumnUnique')) {
    /**
     * 获取数组列的值，且唯一，且去除指定的值。一般用于读取数组里的列值后再in查询使用.
     * @param array $array 数组
     * @param string $columnKey 列的键
     * @param array $diffArr 去除掉的值
     */
    function arrayColumnUnique(array $array, string $columnKey, array $diffArr = [0, '0', null]): array
    {
        return array_diff(array_unique(array_column($array, $columnKey)), $diffArr);
    }
}

if (! function_exists('validateForm')) {
    /**
     * 校验form，且返回trim后的数组.
     * @param $request
     * @param null $modelClass
     */
    function validateForm($request, string $scene = '', $modelClass = null): array
    {
        $newRequest = request($request);
        if ($scene) {
            $newRequest->scene($scene)->validateResolved();
        } else {
            $newRequest->validated();
        }
        if ($modelClass) {
            return trimArr($newRequest->all(), $modelClass::trimFields());
        }
        return $newRequest->all();
    }
}

if (! function_exists('getCheckedBuild')) {
    /**
     * 根据 header 获取选中的楼宇信息.
     */
    function getCheckedBuild(): array
    {
        $checkedBuild = request()->getHeaderLine('checked-build');
        $returnData = [
            'villageIdArr' => [],
            'buildIdArr' => [],
            'buildArr' => [],
        ];
        if ($checkedBuild) {
            $checkedBuildArr = Json::decode($checkedBuild);

            foreach ($checkedBuildArr as $item) {
                $returnData['villageIdArr'][] = $item['id'];
                $returnData['buildIdArr'] = array_merge($returnData['buildIdArr'], $item['build']);
                $returnData['buildArr'][$item['id']] = $item['build'];
            }
        }

        return $returnData;
    }
}
