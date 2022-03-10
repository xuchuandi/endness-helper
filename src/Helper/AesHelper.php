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

/**
 * Class AES数据加密  生成私钥+公钥，加密，解密等等操作.
 */
class AesHelper
{
    /*
     * aes 128
     */
    public const AES_128_ECB = 'AES-128-ECB';

    /*
     * aes 256
     */
    public const AES_256_ECB = 'AES-256-ECB';

    public static function checkSecretKey($secret_key)
    {
        if (! $secret_key) {
            $secret_key = cfg('AES_128_KEY');
        }
        if (! $secret_key) {
            throw new \Exception('未读取到加密key，请稍后重试', ApiHelper::CODE_ERROR);
        }
        return $secret_key;
    }

    /**
     * 加密方法，对数据进行加密，返回加密后的数据.
     *
     * @param string $data 要加密的数据
     * @param mixed $key
     * @param mixed $method
     * @param mixed $iv
     * @param mixed $options
     * @param mixed $secret_key
     */
    public static function encrypt(string $data, string $secret_key = null, string $method = self::AES_128_ECB, string $iv = '', int $options = 0): string
    {
        $secret_key = self::checkSecretKey($secret_key);
        $encryptData = openssl_encrypt($data, $method, $secret_key, $options, $iv);
        return base64_encode($encryptData);
    }

    /**
     * 解密方法，对数据进行解密，返回解密后的数据.
     *
     * @param string $data 要解密的数据
     * @param mixed $secret_key
     * @param mixed $method
     * @param mixed $options
     * @param mixed $iv
     */
    public static function decrypt(string $data, string $secret_key = null, string $method = self::AES_128_ECB, string $iv = '', int $options = 0): bool|string
    {
        $secret_key = self::checkSecretKey($secret_key);
        $decryptData = base64_decode($data);
        return openssl_decrypt($decryptData, $method, $secret_key, $options, $iv);
    }
}
