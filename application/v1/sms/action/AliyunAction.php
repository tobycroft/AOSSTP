<?php

namespace app\v1\sms\action;

use app\v1\log\model\LogSmsModel;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use think\Exception;
use think\facade\Log;

class AliyunAction
{
    public static function Send($type, $tag, $accessid, $accesskey, $sign, $tpcode, $phone, $text)
    {
        $config = [
            'accessKeyId' => $accessid,
            'accessKeySecret' => $accesskey,
        ];

        try {
            $client = new Client($config);
            $sendSms = new SendSms();
            $sendSms->setPhoneNumbers($phone);
            $sendSms->setSignName($sign);
            $sendSms->setTemplateCode($tpcode);
            $sendSms->setTemplateParam(json_decode($text, 320));
//        $sendSms->setOutId('demo');
            $ret = $client->execute($sendSms);
            LogSmsModel::create([
                'oss_type' => $type,
                'oss_tag' => $tag,
                'phone' => $phone,
                'text' => $text,
                'log' => json_encode($ret, 320),
                'success' => false,
            ]);
            Log::debug($ret);
        } catch (Exception $e) {
            LogSmsModel::create([
                "oss_type" => $type,
                "oss_tag" => $tag,
                "phone" => $phone,
                "text" => $text,
                "log" => $e->getMessage(),
                'success' => false,
            ]);
        }

        return $ret;
    }
}