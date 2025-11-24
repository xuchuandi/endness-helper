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

use Endness\Annotation\XxlJobTask;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Codec\Json;
use Hyperf\Di\Annotation\AnnotationCollector;
use function Hyperf\Support\env;

class XxlJobTaskHelper
{
    private array $groupList = [];   // 执行器列表

    private array $jobList = []; //任务列表

    private string $xxlJobCookie = '';  // xxlJob的cookie

    public function build($autoStart = false): void
    {
        // 初始化时校验 xxl-job
        if (env('XXL_JOB_ENABLE') !== true) {
            return;
        }
        //xxl 登录
        $this->xxlJobLogin();

        if (! $this->xxlJobCookie) {
            stdLog()->error('xxlJob登录失败');
            return;
        }

        //xxl 获取组列表
        $this->xxlJobGroupList();

        //xxl 得到组或注册组
        $appName = env('XXL_JOB_APP_NAME');
        $appTitle = env('XXL_JOB_APP_TITLE');
        if (! isset($this->groupList[$appName])) {
            $this->xxlJobGroupAdd($appName, $appTitle);
        }
        $groupId = (int) $this->groupList[$appName]['id'];
        if (! $groupId) {
            stdLog()->error($appName . ' 注册失败');
            return;
        }

        //检测执行器名称有无修改
        if ($this->groupList[$appName]['title'] != $appTitle) {
            $this->xxlJobGroupEdit($groupId, $appName, $appTitle);
        }

        //得到类的所有注解
        $class = AnnotationCollector::getClassesByAnnotation(XxlJobTask::class);

        foreach ($class as $item) {
            //如果不是演示站，则删除「推送企业微信」的任务。
            if (! cfg('system_is_demo') && $item->jobHandler == 'planOrgDemoSitePush') {
                continue;
            }

            //检测获取任务列表
            if (! isset($this->jobList[$groupId])) {
                $this->xxlJobList($groupId);
            }
            if (! isset($this->jobList[$groupId])) {
                stdLog()->error($item->appName . ' 任务列表拉取失败');
                continue;
            }

            //检测任务是否存在
            if (! isset($this->jobList[$groupId][$item->jobHandler])) {
                $tmpData = (array) $item;
                $tmpData['groupId'] = $groupId;
                $this->xxlJobAdd($tmpData);
            }
            if (! isset($this->jobList[$groupId][$item->jobHandler])) {
                stdLog()->error($item->appName . ' 任务注册失败');
                continue;
            }

            $job = $this->jobList[$groupId][$item->jobHandler];

            // 检测任务是否修改
            if (
                $job['jobDesc'] != $item->jobDesc
                || $job['author'] != $item->author
                || $job['scheduleType'] != $item->scheduleType
                || $job['scheduleConf'] != $item->cron
            ) {
                $tmpData = (array) $item;
                $tmpData['groupId'] = $groupId;
                $tmpData['id'] = $job['id'];
                $this->xxlJobEdit($tmpData);
            }

            //自动启动
            if ($autoStart) {
                $this->xxlJobStart((int) $job['id']);
            }
        }
    }

    /**
     * 启动任务.
     */
    private function xxlJobStart(int $jobId)
    {
        $this->getXxlJobData('/jobinfo/start', [
            'id' => $jobId,
        ]);
    }

    /**
     * 获取任务列表.
     */
    private function xxlJobList(int $groupId)
    {
        $res = $this->getXxlJobData('/jobinfo/pageList', [
            'jobGroup' => $groupId,
            'triggerStatus' => '-1',
            'jobDesc' => '',
            'executorHandler' => '',
            'author' => '',
            'start' => '',
            'length' => '1000',
        ]);

        $this->jobList[$groupId] = array_column($res['data'], null, 'executorHandler');
    }

    /**
     * 添加任务.
     * @param mixed $data
     */
    private function xxlJobAdd($data)
    {
        $this->getXxlJobData('/jobinfo/add', [
            'jobGroup' => $data['groupId'],
            'jobDesc' => $data['jobDesc'],
            'author' => $data['author'],
            'alarmEmail' => '',
            'scheduleType' => $data['scheduleType'],
            'scheduleConf' => $data['cron'],
            'cronGen_display' => $data['cron'],
            'schedule_conf_CRON' => $data['cron'],
            'schedule_conf_FIX_RATE' => '',
            'schedule_conf_FIX_DELAY' => '',
            'glueType' => 'BEAN',
            'executorHandler' => $data['jobHandler'],
            'executorParam' => $data['jobParam'],
            'executorRouteStrategy' => $data['routeStrategy'] ?: env('XXL_JOB_EXECUTOR_ROUTE_STRATEGY', 'FAILOVER'), // 故障转移
            'childJobId' => '',
            'misfireStrategy' => 'DO_NOTHING',
            'executorBlockStrategy' => 'SERIAL_EXECUTION',
            'executorTimeout' => (int) $data['jobTimeout'],
            'executorFailRetryCount' => (int) $data['jobRetry'],
            'glueRemark' => 'GLUE代码初始化',
            'glueSource' => '',
        ]);

        $this->xxlJobList($data['groupId']);
    }

    /**
     * 编辑任务.
     * @param mixed $data
     */
    private function xxlJobEdit($data)
    {
        $this->getXxlJobData('/jobinfo/update', [
            'id' => $data['id'],
            'jobGroup' => $data['groupId'],
            'jobDesc' => $data['jobDesc'],
            'author' => $data['author'],
            'alarmEmail' => '',
            'scheduleType' => $data['scheduleType'],
            'scheduleConf' => $data['cron'],
            'cronGen_display' => $data['cron'],
            'schedule_conf_CRON' => $data['cron'],
            'schedule_conf_FIX_RATE' => '',
            'schedule_conf_FIX_DELAY' => '',
            'executorHandler' => $data['jobHandler'],
            'executorParam' => $data['jobParam'],
            'executorRouteStrategy' => $data['routeStrategy'] ?: env('XXL_JOB_EXECUTOR_ROUTE_STRATEGY', 'FAILOVER'), // 故障转移
            'childJobId' => '',
            'misfireStrategy' => 'DO_NOTHING',
            'executorBlockStrategy' => 'SERIAL_EXECUTION',
            'executorTimeout' => (int) $data['jobTimeout'],
            'executorFailRetryCount' => (int) $data['jobRetry'],
        ]);

        $this->xxlJobList($data['groupId']);
    }

    /**
     * 获取执行器列表.
     */
    private function xxlJobGroupList()
    {
        $res = $this->getXxlJobData('/jobgroup/pageList', [
            'appname' => '',
            'title' => '',
            'start' => '',
            'length' => '1000',
        ]);

        $this->groupList = array_column($res['data'], null, 'appname');
    }

    /**
     * 执行器添加.
     * @param mixed $appName
     * @param mixed $title
     */
    private function xxlJobGroupAdd($appName, $title = '')
    {
        if (! $title) {
            $title = $appName;
        }

        $this->getXxlJobData('/jobgroup/save', [
            'appname' => $appName,
            'title' => $title,
            'addressType' => '0',
            'addressList' => '',
        ]);

        $this->xxlJobGroupList();
    }

    /**
     * 执行器添加.
     * @param mixed $appName
     * @param mixed $title
     * @param mixed $id
     */
    private function xxlJobGroupEdit($id, $appName, $title = '')
    {
        if (! $title) {
            $title = $appName;
        }

        $this->getXxlJobData('/jobgroup/update', [
            'appname' => $appName,
            'title' => $title,
            'addressType' => $this->groupList[$appName]['addressType'],
            'addressList' => $this->groupList[$appName]['addressList'],
            'id' => $id,
        ]);

        $this->xxlJobGroupList();
    }

    /**
     * 模拟登录.
     */
    private function xxlJobLogin()
    {
        $userName = cfg('xxljob_userName');
        if (! $userName) {
            $userName = 'admin';
        }

        $password = cfg('xxljob_password');
        if (! $password) {
            $password = '123456';
        }

        $client = new Client();
        try {
            $res = $client->request('POST', env('XXL_JOB_ADMIN_ADDRESS') . '/login', [
                'form_params' => [
                    'userName' => $userName,
                    'password' => $password,
                ],
            ]);

            $this->xxlJobCookie = (string) $res->getHeaderLine('set-cookie');
        } catch (GuzzleException $e) {
            stdLog()->error('xxl-job登录失败：' . $e->getMessage());
        }
    }

    /**
     * xxlJob的请求.
     * @return array|mixed
     */
    private function getXxlJobData(string $uri, array $formParams = [])
    {
        $client = new Client();
        try {
            $response = $client->request('POST', env('XXL_JOB_ADMIN_ADDRESS') . $uri, [
                'headers' => [
                    'Cookie' => $this->xxlJobCookie,
                ],
                'allow_redirects' => true,
                'form_params' => $formParams,
            ]);
        } catch (GuzzleException $e) {
            stdLog()->error('xxl-job执行' . $uri . '失败：' . $e->getMessage());
        }

        $body = (string) $response->getBody(); //获取响应体，对象
        $bodyArr = Json::decode($body, true);

        return $bodyArr ?: [];
    }
}
