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
namespace Endness\JsonRpc\Village;

interface VillageServiceInterface
{
    /**
     * 获取项目和楼栋组合好的数据.
     */
    public function getVillageAndBuild(array $villageIdArr, array $buildArr = [], bool $mergeData = true): array;

    /**
     * 通过楼栋ID获取楼栋组合好的数据.
     */
    public function getBuildList(array $buildArr = []): array;

    /**
     * 填充房间的合同信息.
     */
    public function setRoomContract(int $roomId, array $contractArr): array;

    /**
     * 获取指定项目或楼栋房间闲置信息.
     */
    public function getVacantVillageRoom(array $villageIdArr, array $buildArr = [], array $whereDate = [], int $limit = 0): array;

    /**
     * 根据指定query获取village数据.
     */
    public function getVillageRoomByQuery(string $query): array;

    /**
     * 根据房间ID获取楼层列表数据.
     */
    public function getRooms(array $roomArr = []): array;

    /**
     * 根据楼层ID获取楼层列表数据.
     */
    public function getLayers(array $layerArr = []): array;
}
