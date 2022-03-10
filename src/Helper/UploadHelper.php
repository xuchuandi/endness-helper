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

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use League\Flysystem\Filesystem;

/**
 * Class UploadHelper.
 */
class UploadHelper
{
    /**
     * @Inject
     * @var ConfigInterface
     */
    private $config;

    public function __construct()
    {
        // 重新从数据库读 cos 配置项
        $this->config->set('file.storage.cos.region', cfg('filesystem_qcloud_region'));
        $this->config->set('file.storage.cos.app_id', cfg('qcloud_appid'));
        $this->config->set('file.storage.cos.secret_id', cfg('qcloud_secret_id'));
        $this->config->set('file.storage.cos.secret_key', cfg('qcloud_secret_key'));
        $this->config->set('file.storage.cos.bucket', cfg('filesystem_qcloud_bucket'));
    }

    /**
     * 上传文件.
     * @param mixed $file
     * @param mixed $fileDir
     */
    public function uploadFile($file, $fileDir): array
    {
        $filesystem = new Filesystem();
        if (! $fileDir) {
            $fileDir = 'files';
        }

        if (! $file) {
            return ApiHelper::genErrorData('请上传文件');
        }

        $allowExtension = ['png', 'gif', 'jpeg', 'jpg', 'zip', 'txt', 'xls', 'doc', 'docs', 'pdf', 'rar'];
        if (! in_array($file->getExtension(), $allowExtension)) {
            return ApiHelper::genErrorData('文件格式（' . $file->getExtension() . '）不允许，只允许 ' . implode('，', $allowExtension));
        }

        /*
         * 文件Mime暂不校验
         *
        $allowMimeType = ['image/png', 'image/gif', 'image/jpeg', 'application/zip', 'text/plain', 'application/vnd.ms-excel', 'application/msword'];
        if (! in_array($file->getMimeType(), $allowMimeType)) {
            return ApiHelper::genErrorData('图片mime类型（' . $file->getMimeType() . '）不允许，只允许 ' . implode('，', $allowMimeType));
        }
        */

        $stream = fopen($file->getRealPath(), 'r+');

        $fileName = 'upload/' . $fileDir . '/' . date('Ymd') . '/' . uniqid() . mt_rand(10000, 99999) . '.' . $file->getExtension();
        $filesystem->writeStream(
            $fileName,
            $stream
        );
        fclose($stream);

        //为了后续纠出传不合法文件人的信息，记入日志。
        logger()->info('uploadFile' . $fileName, $this->request->getHeaders());

        return ApiHelper::genSuccessData(['fileName' => $fileName, 'fileUrl' => fileDomain($fileName)]);
    }

    /**
     * 上传图片.
     * @param mixed $file
     * @param mixed $fileDir
     */
    public static function uploadImage($file, $fileDir): array
    {
        $filesystem = new Filesystem();
        if (! $fileDir) {
            $fileDir = 'images';
        }

        if (! $file) {
            return ApiHelper::genErrorData('请上传文件');
        }

        $allowExtension = ['png', 'gif', 'jpeg', 'jpg'];
        if (! in_array($file->getExtension(), $allowExtension)) {
            return ApiHelper::genErrorData('图片格式（' . $file->getExtension() . '）不允许，只允许 ' . implode('，', $allowExtension));
        }

        $allowMimeType = ['image/png', 'image/gif', 'image/jpeg'];
        if (! in_array($file->getMimeType(), $allowMimeType)) {
            return ApiHelper::genErrorData('图片mime类型（' . $file->getMimeType() . '）不允许，只允许 ' . implode('，', $allowMimeType));
        }

        $stream = fopen($file->getRealPath(), 'r+');

        $fileName = 'upload/' . $fileDir . '/' . date('Ymd') . '/' . uniqid() . mt_rand(10000, 99999) . '.' . $file->getExtension();
        $filesystem->writeStream(
            $fileName,
            $stream
        );
        fclose($stream);

        //为了后续纠出传不合法文件人的信息，记入日志。
        logger()->info('uploadImage' . $fileName);

        return ApiHelper::genSuccessData(['fileName' => $fileName, 'fileUrl' => fileDomain($fileName)]);
    }
}
