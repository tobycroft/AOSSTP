<?php

namespace app\v1\sms\action;

use app\v1\log\model\LogSmsModel;
use app\v1\sms\struct\SendStdErr;
use Qcloud\Sms\SmsSingleSender;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\SmsClient;

// 导入要请求接口对应的Request类

// 导入可选配置类

class TencentSmsAction
{
    public static function Send(mixed $type, $tag, $appid, $appkey, int $quhao, string|array $phone, mixed $text, $smsSign, $templateId): SendStdErr
    {
        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $params = json_decode($text, 1);
            $result = $ssender->sendWithParam($quhao, $phone, $templateId, $params, $smsSign, '', '');
            $ret = json_decode($result);
            echo $result;

            $success = false;
            if (strtolower($ret->ActionStatus) == 'ok') {
                $success = true;
            }
            LogSmsModel::create([
                'oss_type' => $type,
                'oss_tag' => $tag,
                'phone' => $phone,
                'text' => $text,
                'raw' => json_encode($ret, 320),
                'log' => $ret->Message,
                'success' => $success,
                'error' => false,
            ]);
            if ($success) {
                return new SendStdErr(0, null, $ret->Message);
            } else {
                return new SendStdErr(200, null, $ret->Message);
            }
        } catch (TencentCloudSDKException $e) {
            LogSmsModel::create([
                'oss_type' => $type,
                'oss_tag' => $tag,
                'phone' => $phone,
                'text' => $text,
                'log' => $e->getMessage(),
                'raw' => $e->getTraceAsString(),
                'success' => false,
                'error' => true,
            ]);
            return new SendStdErr(500, null, $e->getMessage());
        }
    }
}