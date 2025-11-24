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

/**
 * 图片辅助类.
 */
class ImageHelper
{
    /**
     * base64加密图片.
     * @param string $image_file 图片路径
     * @param bool $prefix 是否需要前缀
     */
    public static function base64EncodeImage(string $image_file, bool $prefix = false): string
    {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        if (! $prefix) {
            return base64_encode($image_data);
        }
        return 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    }
}
