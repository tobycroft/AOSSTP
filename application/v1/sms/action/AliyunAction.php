<?php

namespace app\v1\sms\action;

use app\v1\log\model\LogSmsModel;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class AliyunAction
{
    public static function Send($type, $tag, $accessid, $accesskey, $sign, $tpcode, $phone, $text): string|null
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
            $success = false;
            if (strtolower($ret->Code) == "ok") {
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
                return null;
            } else {
                return $ret['Message'];
            }
        } catch (\Throwable $e) {
            LogSmsModel::create([
                "oss_type" => $type,
                "oss_tag" => $tag,
                "phone" => $phone,
                "text" => $text,
                "log" => $e->getMessage(),
                "raw" => $e->getTraceAsString(),
                'success' => false,
                'error' => true,
            ]);
            return $e->getMessage();
        }
    }
}