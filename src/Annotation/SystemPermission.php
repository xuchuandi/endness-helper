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
namespace Endness\Annotation;

        use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class SystemPermission extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $module = ''; // 管理后台:系统设置:角色管理

    /**
     * @var string
     */
    public $action = ''; // 查看、增加、修改、删除

    /**
     * @var string
     */
    public $icon = ''; // 参考: http://layuimini.99php.cn/iframe/v2/index.html#/page/icon.html

    /**
     * @var string
     */
    public $menu_type = '1'; // 1展示，0归类

    /**
     * @var string
     */
    public $url_type = 'path'; // 	URL类别(path, frame_url, target_url)

    /**
     * @var string
     */
    public $alias = ''; // 路由别名

    /**
     * @var string
     */
    public $param = ''; // 路由参数

    /**
     * @var string
     */
    public $sort = '0'; // 排序，越大越前，一级菜单千进位，二级菜单百进位，默认0

    /**
     * @var string
     */
    public $status = '1'; // 状态，0和1，默认1

    /**
     * @var string
     */
    public $app = ''; // 微前端提供者

    /*
     * 使用CURD.
     * @var string[]
     */
    /*public $actions = [
        'list' => '查看列表',
        'info' => '查看详情',
        'create' => '增加一条',
        'delete' => '删除一条',
        'deleteMulti' => '删除批量',
        'update' => '修改一条',
        'resume' => '恢复一条',
        'resumeMulti' => '恢复批量',
    ];*/
}
