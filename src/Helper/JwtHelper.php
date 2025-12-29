<?php
/**
 * JWT相关.
 */
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

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hyperf\Stringable\Str;
use Hyperf\Context\Context;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class JwtHelper
{
    public const SYSTEM_JWT_TOKEN = 'system-token';

    public const ORG_JWT_TOKEN = 'org-token';

    public const USER_JWT_TOKEN = 'user-token';

    public const GUEST_JWT_TOKEN = 'guest-token';

    /**
     * 生成.
     * @param mixed $info
     */
    public static function encode(?array $info, string $label = 'SYSTEM', string $type = 'HS256'): string
    {
        $data = [
            'data' => $info,
            'iat' => time(),
            'jti' => Str::random(8),
        ];
        return JWT::encode($data, self::getKey($label), $type);
    }

    /**
     * 从请求中解密.
     */
    public static function decodeWithRequest(string $keyLabel = 'SYSTEM', ?ServerRequestInterface $request = null, string $type = 'HS256'): ?object
    {
        if ($keyLabel == 'SYSTEM') {
            $key = self::SYSTEM_JWT_TOKEN;
        } elseif ($keyLabel == 'ORG') {
            $key = self::ORG_JWT_TOKEN;
        } elseif ($keyLabel == 'USER') {
            $key = self::USER_JWT_TOKEN;
        }

        if (empty($key)) {
            return null;
        }
        $request || $request = Context::get(ServerRequestInterface::class);
        if (! $request) {
            return null;
        }

        $jwt = $request->getHeaderLine($key);
        if ($jwt) {
            return self::decode($jwt, $keyLabel, $type);
        }
        return null;
    }

    /**
     * 解密.
     */
    public static function decode(string $token, string $label = 'SYSTEM', string $type = 'HS256'): ?object
    {
        if (empty($token)) {
            return null;
        }
        try {
            return JWT::decode($token, new Key(self::getKey($label), $type));
        } catch (Throwable $throwable) {
            return null;
        }
    }

    public static function dataToHash(?object $jwtData): ?string
    {
        if (is_null($jwtData)) {
            return null;
        }
        $jti = $jwtData->jti ?? null;
        if (is_null($jti)) {
            return null;
        }
        $jwtData = json_encode($jwtData);
        return $jti . md5($jwtData);
    }

    /**
     * 获取用户登录信息.
     */
    public static function userData(null|string $keyLabel = 'USER'): object|null
    {
        $userData = self::decodeWithRequest($keyLabel);
        return $userData?->data;
    }

    /**
     * 获取密钥.
     */
    private static function getKey(string $label = 'SYSTEM'): string
    {
        $keyName = 'JWT_' . $label . '_KEY';
        $key = cfg($keyName);
        if (! $key) {
            return '';
        }
        return $key;
    }
}
