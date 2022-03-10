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

use Endness\Annotation\OrgPermission;
use Endness\JsonRpc\Org\OrgMenuServiceInterface;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Codec\Json;

class OrgPermissionHelper
{
    private string $micro = '';  //  微服务名称

    private string $microAuto = '1';  //  系统后台菜单自增ID

    private array $moduleWithSons = [];   //  module的父和子归集名称

    private array $moduleActions = [];    //  module对应的操作列表

    private array $modules = [];  //  注解所有的集合，以module为下标

    private array $moduleMenuId = []; //  module入库的自增id

    public function __construct()
    {
        $this->micro = env('MICRO_NAME', env('APP_NAME'));

        // env设定，不同的项目10000自增
        $defaultAuto = (config('server.servers.0.port') - 9501) * 10000 + 1;
        $this->microAuto = env('MICRO_ORG_MENU_AUTO', strval($defaultAuto));
    }

    public function build(): void
    {
        $menuData = $this->prepare();

        $client = ApplicationContext::getContainer()->get(OrgMenuServiceInterface::class);
        logger()->info('org menuData', $menuData);
        $client->addMenu($menuData);
    }

    public function prepare(): array
    {
        $class = AnnotationCollector::getClassesByAnnotation(OrgPermission::class);

        foreach ($class as $key => $value) {
            $tmpModule = $value->module;
            $this->modules[$tmpModule] = ['annotation' => $value];

            if (strpos($tmpModule, ':') > 0) {
                $this->setModuleSon($tmpModule);
            } else {
                $this->moduleWithSons[$tmpModule] = [];
            }
        }

        //得到类方法的所有注解
        $methods = AnnotationCollector::getMethodsByAnnotation(OrgPermission::class);

        $parentOrgIdArr = [];

        foreach ($methods as $value) {
            $tmpModule = $value['annotation']->module;

            if ($value['annotation']->parentOrgId) {
                $parentOrgIdArr[$tmpModule] = $value['annotation']->parentOrgId;
            } elseif (isset($parentOrgIdArr[$tmpModule])) {
                $value['annotation']->parentOrgId = $parentOrgIdArr[$tmpModule];
            }

            if (strpos($tmpModule, ':') > 0) {
                if ($value['annotation']->parentOrgId) {
                    $this->moduleWithSons[$tmpModule] = [];
                    $tmpNameArr = explode(':', $tmpModule);
                    $tmpModule = array_pop($tmpNameArr);
                    $this->moduleWithSons[$tmpModule] = [];
                } else {
                    $this->setModuleSon($tmpModule);
                }
            } else {
                $this->moduleWithSons[$tmpModule] = [];
            }

            if (! isset($this->modules[$tmpModule]) || (
                empty($this->modules[$tmpModule]['annotation']->icon)
                    && empty($this->modules[$tmpModule]['annotation']->alias)
                    && empty($this->modules[$tmpModule]['annotation']->sort)
            )
            ) {
                $this->modules[$tmpModule] = $value;
            }

            if ($value['annotation']->action) {
                $this->setActions($tmpModule, $value['annotation']->action, $value['class'] . ':' . $value['method']);
            }
        }
        //var_dump($methods);
        //var_dump($this->moduleWithSons);
        //var_dump($this->moduleActions);

        // 创建临时表存储数据
        $this->createMemTable();

        // 遍历父名称节点，进行排序，一级、二级
        $tmpArr = [];
        foreach ($this->moduleWithSons as $key => $value) {
            $tmpArr[$key] = substr_count($key, ':');
        }
        asort($tmpArr);

        //  遍历父名称节点
        foreach ($tmpArr as $key => $value) {
            $this->saveMenu($key);
        }

        foreach ($this->moduleWithSons as $key => $value) {
            // 遍历子名称节点
            foreach ($value as $item) {
                $this->saveMenu($item);
            }
        }

        //获取临时表的数据
        $menuArr = $this->getMemTableData();

        //删除表
        $this->dropMemTable();

        return [
            'micro' => $this->micro,
            'menuArr' => $menuArr,
        ];
    }

    /*
     * 遍历父子关系入库.
     */
    private function saveMenu($moduleName)
    {
        //var_dump($moduleName);
        //var_dump($this->moduleMenuId);
        if (isset($this->moduleMenuId[$moduleName])) {
            return;
        }

        if (isset($this->modules[$moduleName])) {
            $module = $this->modules[$moduleName];
            $annotation = $this->modules[$moduleName]['annotation'];
        } else {
            $module = null;
            $annotation = new \stdClass();
        }

        $tmpModuleArr = explode(':', $moduleName);
        $name = array_pop($tmpModuleArr);
        $parent = implode(':', $tmpModuleArr);

        //固定父级ID的菜单，只读右侧。
        if (isset($annotation->parentOrgId) && $annotation->parentOrgId) {
            $annotation->module = $name;
        }

        $insertData = [
            'name' => $name,
            'parent_id' => $this->moduleMenuId[$parent] ?? 0,
            'url_type' => $annotation->urlType ?? null,
            'icon' => $annotation->icon ?? null,
            'active_icon' => $annotation->activeIcon ?? null,
            'menu_type' => $annotation->menuType ?? '1',
            'alias' => $annotation->alias ?? null,
            'param' => $annotation->param ?? null,
            'sort' => $annotation->sort ?? '0',
            'action' => Json::encode(isset($annotation->module, $this->moduleActions[$annotation->module]) && $this->moduleActions[$annotation->module] ? $this->moduleActions[$annotation->module] : []),
            'status' => $annotation->status ?? '1',
            'app' => $annotation->app ?? null,
            'micro' => $this->micro,
            'org_id' => $annotation->orgId ?? '0',
            'org_parent_id' => $annotation->parentOrgId ?? '0',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $hasMenu = Db::table('temp_org_menu')->where('name', $insertData['name'])->where('parent_id', $insertData['parent_id'])->first();
        if (! $hasMenu) {
            $this->moduleMenuId[$moduleName] = Db::table('temp_org_menu')->insertGetId($insertData);
        }
    }

    /*
     * 设置actions归集到对象里，方便后续通过菜单名直接取到动作数组.
     */
    private function setActions(string $module, string $actions, string $classMethod): void
    {
        //var_dump($module);
        //支持多个actions
        $actionsArr = explode(',', $actions);
        foreach ($actionsArr as $item) {
            $actionLites = explode(':', $item);
            $actionTmp = explode('-', $actionLites[1]);

            key_exists($module, $this->moduleActions) || $this->moduleActions[$module] = [];
            key_exists($actionLites[0], $this->moduleActions[$module]) || $this->moduleActions[$module][$actionLites[0]] = [];
            $this->moduleActions[$module][$actionLites[0]][] = [
                'name' => $actionTmp[0],
                'label' => $actionTmp[1],
                'classMethod' => $classMethod,
            ];
        }
    }

    /*
     * 生成父子对应的菜单数组.
     */
    private function setModuleSon(string $module): void
    {
        $moduleLites = explode(':', $module);
        array_pop($moduleLites);
        $moduleFather = implode(':', $moduleLites);

//        var_dump('----- start');
//        var_dump($module);
//        var_dump($moduleLites);
//        var_dump($moduleFather);
//        var_dump($this->moduleWithSons);
//        var_dump('----- end');
        if (! empty($moduleFather)) {
            key_exists($moduleFather, $this->moduleWithSons) || $this->moduleWithSons[$moduleFather] = [];
            if (! in_array($module, $this->moduleWithSons[$moduleFather], true)) {
                array_push($this->moduleWithSons[$moduleFather], $module);
            }
            $this->setModuleSon($moduleFather);
        }
    }

    private function createMemTable()
    {
        $createTableSql = "CREATE TEMPORARY TABLE `temp_org_menu` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `parent_id` int NOT NULL DEFAULT '0' COMMENT '父级id',
  `url_type` enum('path','frame_url','target_url') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL类别',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '菜单图标',
  `active_icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '菜单图标(选中)',
  `menu_type` tinyint NOT NULL DEFAULT '1' COMMENT '1展示，0归类',
  `alias` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '前端路由对应的别名，用于和前端通信',
  `param` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '路由参数',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序，越大越前，一级菜单千进位，二级菜单百进位',
  `action` json DEFAULT NULL COMMENT '操作集',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '显示状态，1显示，0隐藏',
  `app` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '微前端提供者',
  `micro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '微服务提供者',
  `org_id` int NOT NULL DEFAULT '0' COMMENT '原始固定自增ID',
  `org_parent_id` int NOT NULL DEFAULT '0' COMMENT '原始固定自增父ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统后台菜单表';";

        try {
            Db::statement($createTableSql);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }

        try {
            Db::table('temp_org_menu')->truncate();
            Db::statement('ALTER TABLE `temp_org_menu`  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=' . $this->microAuto);

            echo 'ALTER TABLE `temp_org_menu`  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=' . $this->microAuto;
        } catch (\Exception $exception) {
            stdLog()->error($exception->getMessage());
        }
    }

    /*
     * 删除临时表.
     */
    private function dropMemTable()
    {
        try {
            Db::statement('drop temporary table `temp_org_menu`;');
        } catch (\Exception $exception) {
        }
    }

    /*
     * 获取临时表的所有数据.
     */
    private function getMemTableData(): array
    {
        $menuData = Db::table('temp_org_menu')->orderByDesc('org_id')->orderByDesc('org_parent_id')->get();
        $return = $menuData->toArray();
        foreach ($return as &$item) {
            $item = (array) $item;
        }
        return $return;
    }
}
