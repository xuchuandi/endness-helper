<?php
/**
 * 请求相关.
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

use TencentCloud\Captcha\V20190722\CaptchaClient;
use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;

class QcloudCaptchaHelper
{
    /**
     * 检查验证码是否正确.
     */
    public static function checkCaptcha(string $captchaTicket, string $captchaRandstr): array
    {
        try {
            // ticket 校验
            if (! $captchaTicket) {
                throw new TencentCloudSDKException(ApiHelper::CODE_ERROR, __('messages.qcloud_captcha_ticket_error'));
            }
            if (! $captchaRandstr) {
                throw new TencentCloudSDKException(ApiHelper::CODE_ERROR, __('messages.qcloud_captcha_randstr_error'));
            }
            $cred = new Credential(cfg('qcloud_secret_id'), cfg('qcloud_secret_key'));
            $captchaClient = new CaptchaClient($cred, cfg('captcha_qcloud_region'));
            $req = new DescribeCaptchaResultRequest();
            $req->setCaptchaAppId(intval(cfg('captcha_qcloud_appid')));
            $req->setAppSecretKey(cfg('captcha_qcloud_app_secret_key'));
            $req->setTicket($captchaTicket);
            $req->setRandstr($captchaRandstr);
            $req->setCaptchaType(9);
            $req->setUserIp(RequestHelper::getClientIp());
            $captchaResult = $captchaClient->DescribeCaptchaResult($req);
            if ($captchaResult->CaptchaCode != 1) {
                throw new TencentCloudSDKException(ApiHelper::NORMAL_ERROR, $captchaResult->CaptchaMsg . '，code：' . $captchaResult->CaptchaCode);
            }
            return ApiHelper::genSuccessData((array) $captchaResult);
        } catch (TencentCloudSDKException $e) {
            $errorCode = $e->getErrorCode();
            //如果不是try自行验证的代码错误，直接返回 普通错误。
            if ($errorCode != ApiHelper::CODE_ERROR) {
                $errorCode = ApiHelper::CODE_ERROR;
            }
            return ApiHelper::genErrorData(__('messages.qcloud_captcha_tip') . '：' . $e->getMessage(), $errorCode);
        }
    }
}
