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

use Hyperf\Di\Exception\Exception;
use Vtiful\Kernel\Excel;

class XlsWriterHelper
{
    /**
     * @params string $folder 保存下的文件夹名称
     */
    public function __construct(
        private string $folder = 'viest',
    ) {
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(string $folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * 初始化构造xlsWriter类.
     * @throws Exception
     */
    public function init()
    {
        $config = [
            'path' => RUNTIME_BASE_PATH . '/xls/' . $this->folder, // xlsx文件保存路径
        ];
        if (! $this->checkLoadedExtension()) {
            throw new Exception('xlsWriter extension is not installed, please install extension');
        }
        //创建文件路径
        self::makeDir($config['path']);
        return new class($config) extends Excel {};
    }

    /**
     * Notes: 校验当前环境是否安装xlsWriter扩展
     * Author: Endness
     * Date: 2022/4/25 16:51.
     */
    private function checkLoadedExtension(): bool
    {
        return extension_loaded('xlswriter');
    }

    /**
     * 创建文件路径.
     * @param string $path 文件路径
     */
    private static function makeDir(string $path): bool
    {
        //判断目录存在否，存在给出提示，不存在则创建目录
        if (is_dir($path)) {
            stdLog()->debug('目录 ' . $path . ' 已经存在！');
            return true;
        }

        $res = mkdir($path, 0777, true);
        if ($res) {
            stdLog()->debug("目录 {$path} 创建成功");
            return true;
        }

        stdLog()->error("xlsWriter 目录 {$path} 创建失败");
        return false;
    }
}
