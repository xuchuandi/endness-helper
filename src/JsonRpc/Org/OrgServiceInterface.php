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
namespace Endness\JsonRpc\Org;

interface OrgServiceInterface
{
    /**
     * 添加机构和项目绑定关系.
     * @param array $data 二维数组 [['org_id' => '', 'village_id' => ''], ['org_id' => '', 'village_id' => '']]
     */
    public function addOrgVillageRelation(array $data): array;

    /**
     * 根据ID数组获取房源标签列表.
     */
    public function getTagHouseList(array $tagIdArr): array;

    /**
     * 根据ID数组获取楼栋标签列表.
     */
    public function getTagBuildList(array $tagIdArr): array;

    /**
     * 根据ID数组获取项目标签列表.
     */
    public function getTagVillageList(array $tagIdArr): array;

    /**
     * 根据ID 获取用户在机构内的信息.
     */
    public function getUserByUserIdArr(array $userIdArr): array;

    /**
     * 根据ID 获取机构的基本信息.
     */
    public function getOrgByOrgIdArr(array $orgIdArr): array;

    /**
     * 根据ID 获取租客基本信息列表.
     */
    public function getOwnerByIdArr(array $idArr): array;

    /**
     * 根据指定query获取机构指定数据.
     */
    public function getOrgDataByQuery(string $query): array;

    /**
     * 根据指定owner_id获取合同关联房间信息.
     */
    public function getRoomIdByOwner(int $ownerId): array;

    /**
     * 根据指定owner_id/指定条件获取机构账单信息.
     */
    public function getBillListByOwner(int $ownerId, array $where): array;

    /**
     * 根据指定账单ID获取账单详情(含子账单明细).
     */
    public function getBillDetailById(int $billId, array $where): array;
}
