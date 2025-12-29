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
namespace Endness\Helper;

class ApiHelper
{
    /*
     * 接口正常响应.
     */
    public const NORMAL_SUCCESS = 0;

    /*
     * 代码传参缺少等错误，前端需要自查代码.
     */
    public const CODE_ERROR = 1005;

    /*
     * 服务通信发生的错误（致命），需要存储信息并且及时检查.
     */
    public const SERVICE_ERROR = 1010;

    /*
     * 普通业务错误，前端弹层提示.
     */
    public const NORMAL_ERROR = 1001;

    /*
     * 登录超时，前端弹层提示，且做登录操作.
     */
    public const LOGIN_ERROR = 1002;

    /*
     * 权限错误.
     */
    public const AUTH_ERROR = 1003;

    /*
     * 错误级别，notice、warning、error。默认 warning
     */
    public const ERROR_LEVEL_WARNING = 'warning';

    public const ERROR_LEVEL_NOTICE = 'notice';

    public const ERROR_LEVEL_ERROR = 'error';

    /**
     * 成功返回.
     * @param null|mixed $data
     */
    public static function genSuccessData(?array $data = null, string $msg = ''): array
    {
        $re = [
            'code' => self::NORMAL_SUCCESS,
            'msg' => $msg ?: 'OK',
        ];
        if (is_array($data) && isset($data[0])) {
            $data = ['error' => '返回值被拦截。data必须是对象，不能是数组！请调整接口。'];
        }
        is_null($data) || $re['data'] = $data;
        return $re;
    }

    /**
     * 失败返回.
     * @param mixed|string $msg
     * @param int|mixed $code
     * @param mixed|string $errorLevel 默认警告
     */
    public static function genErrorData(?string $msg = null, int $code = self::NORMAL_ERROR, string $errorLevel = self::ERROR_LEVEL_WARNING): array
    {
        $code = intval($code);
        $code == 0 && $code = self::NORMAL_ERROR;

        if ($code == self::CODE_ERROR) {
            $msg = '[Code Error]：' . $msg;
        }
        return [
            'code' => $code,
            'msg' => $msg,
            'errorLevel' => $errorLevel,
        ];
    }

    /**
     * 数据库未查找到数据的错误返回，目的是为了统一返回文本.
     * @param mixed|string $msg
     * @param int|mixed $code
     * @param mixed|string $errorLevel 默认警告
     */
    public static function genErrorDataEmpty(?string $msg = null, int $code = self::NORMAL_ERROR, string $errorLevel = self::ERROR_LEVEL_WARNING): array
    {
        if (! $msg) {
            $msg = '未查询到该条数据，请检查该数据是否存在';
        }
        return self::genErrorData($msg, $code, $errorLevel);
    }

    /**
     * 服务通信失败返回.
     * @param $serviceName
     * @param $exception
     */
    public static function genServiceErrorData(string $serviceName, \Exception $exception): array
    {
        $msg = sprintf('服务异常（%s）', $serviceName);
        logger()->error($msg, [$exception->getCode(), $exception->getMessage()]);
        return self::genErrorData(sprintf('%s[%s]', $msg, $exception->getMessage()), ApiHelper::SERVICE_ERROR);
    }

    /**
     * 检测本类生成的数据，成功或失败.
     * @param $dataArr
     */
    public static function checkDataOk($dataArr): bool
    {
        return $dataArr['code'] == self::NORMAL_SUCCESS;
    }
}
