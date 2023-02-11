<?php

namespace app\v1\sms\action;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use think\facade\Log;

class AliyunAction
{
    public static function Send($accessid, $accesskey, $sign, $tpcode, $phone, $param)
    {
        $config = [
            'accessKeyId' => $accessid,
            'accessKeySecret' => $accesskey,
        ];

        $client = new Client($config);
        $sendSms = new SendSms();
        $sendSms->setPhoneNumbers($phone);
        $sendSms->setSignName($sign);
        $sendSms->setTemplateCode($tpcode);
        $sendSms->setTemplateParam($param);
//        $sendSms->setOutId('demo');
        $ret = $client->execute($sendSms);
        Log::debug($ret);
        return $ret;
    }
}