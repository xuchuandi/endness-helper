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

use Endness\Helper\ApiHelper;
use Hyperf\RpcClient\AbstractServiceClient;

class VillageServiceConsumer extends AbstractServiceClient implements VillageServiceInterface
{
    /**
     * 定义对应服务提供者的服务名称.
     * @var string
     */
    protected $serviceName = 'VillageService';

    /**
     * 定义对应服务提供者的服务协议.
     * @var string
     */
    protected $protocol = 'jsonrpc-http';

    /**
     * 添加菜单.
     */
    public function getVillageAndBuild(array $villageIdArr, array $buildArr = [], bool $mergeData = true): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('villageIdArr', 'buildArr', 'mergeData'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 通过楼栋ID获取楼栋组合好的数据.
     */
    public function getBuildList(array $buildArr = []): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('buildArr'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 填充房间的合同信息.
     */
    public function setRoomContract(int $roomId, array $contractArr): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('roomId', 'contractArr'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 获取指定项目或楼栋房间闲置信息.
     * @param mixed $limit
     */
    public function getVacantVillageRoom(array $villageIdArr, array $buildArr = [], array $whereDate = [], $limit = 0): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('villageIdArr', 'buildArr', 'whereDate', 'limit'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 根据指定query获取village数据.
     * @param string
     */
    public function getVillageRoomByQuery(string $query): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('query'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 根据房间ID获取楼层列表数据.
     */
    public function getRooms(array $roomArr = []): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('roomArr'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }

    /**
     * 根据楼层ID获取楼层列表数据.
     */
    public function getLayers(array $layerArr = []): array
    {
        try {
            return $this->__request(__FUNCTION__, compact('layerArr'));
        } catch (\Exception $exception) {
            return ApiHelper::genServiceErrorData($this->serviceName, $exception);
        }
    }
}
