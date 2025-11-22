<?php

declare(strict_types=1);
/**
 * This file is part of Kuaijing Bailing.
 *
 * @link     https://www.kuaijingai.com
 * @document https://help.kuaijingai.com
 * @contact  www.kuaijingai.com 7*12 9:00-21:00
 */
namespace Endness\Helper;

class FileHelper
{
    /**
     * 递归获取目录下的文件列表（不包含目录）.
     */
    public static function getDirFiles(string $path, bool $fileName = true): array
    {
        return self::getDirFilesHandle(new \RecursiveDirectoryIterator($path), $fileName);
    }

    /**
     * 获取目录下的文件列表（包含目录，非递归）.
     */
    public static function getDir(string $path, string $replacePath = ''): array
    {
        $dir = new \RecursiveDirectoryIterator($path);

        $files = [];
        for (; $dir->valid(); $dir->next()) {
            if (! $dir->isDot()) {
                $files[] = [
                    'isDir' => $dir->isDir(),
                    'fileName' => $dir->getFilename(),
                    'pathName' => $replacePath ? str_replace($replacePath, '', $dir->getPathName()) : $dir->getPathName(),
                    'mTime' => $dir->getMTime(),
                    'mTimeTxt' => date('Y-m-d H:i:s', $dir->getMTime()),
                    'aTime' => $dir->getATime(),
                    'aTimeTxt' => date('Y-m-d H:i:s', $dir->getATime()),
                    'cTime' => $dir->getCTime(),
                    'cTimeTxt' => date('Y-m-d H:i:s', $dir->getCTime()),
                    'size' => $dir->getSize(),
                    'ext' => $dir->getExtension(),
                ];
            }
        }

        return $files;
    }

    /**
     * 获取文件内容.
     */
    public static function getContent(string $fileName): bool|string
    {
        return file_get_contents($fileName);
    }

    /**
     * 递归获取目录下的文件列表（不包含目录）.
     * @param mixed $dir
     */
    private static function getDirFilesHandle($dir, bool $fileName = true): array
    {
        $files = [];
        for (; $dir->valid(); $dir->next()) {
            if ($dir->isDir() && ! $dir->isDot()) {
                if ($dir->haschildren()) {
                    $files = array_merge($files, self::getDirFilesHandle($dir->getChildren(), $fileName));
                }
            } elseif ($dir->isFile()) {
                if ($fileName) {
                    $files[] = $dir->getPathName();
                } else {
                    $files[] = [
                        'isDir' => $dir->isDir(),
                        'fileName' => $dir->getFilename(),
                        'pathName' => $dir->getPathName(),
                        'mTime' => $dir->getMTime(),
                        'aTime' => $dir->getATime(),
                        'cTime' => $dir->getCTime(),
                        'size' => $dir->getSize(),
                        'ext' => $dir->getExtension(),
                    ];
                }
            }
        }

        return $files;
    }
}
