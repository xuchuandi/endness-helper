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
namespace Endness\JsonRpc\Message;

interface NoticeServiceInterface
{
    /**
     * 添加机构通知消息.
     */
    public function addOrgNotice(int $org_id, int $uid, string $title, string $content): array;
}
