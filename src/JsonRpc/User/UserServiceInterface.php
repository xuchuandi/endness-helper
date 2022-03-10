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
namespace Endness\JsonRpc\User;

interface UserServiceInterface
{
    /**
     * 通过手机号找用户.
     */
    public function getUserByPhone(string $phone, int $phoneCountry = 86): array;

    /**
     * 通过uid集合找用户.
     */
    public function getUserByUids(array $uidArr): array;

    /**
     * 通过uid和appid找对应的用户第三方授权信息.
     */
    public function getUserThirdByUidAndAppid(int $uid, string $appid): array;

    /**
     * 绑定用户第三方信息.
     */
    public function addUserThird(int $user_id, string|null $appid, array $userInfo): array;
}
