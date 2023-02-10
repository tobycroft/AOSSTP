<?php

namespace app\v1\sms\action;

class AliyunAction
{
    public static function Send()
    {
        $config = [
            'accessKeyId' => 'LTAIbVA2LRQ1tULr',
            'accessKeySecret' => 'ocS48RUuyBPpQHsfoWokCuz8ZQbGxl',
        ];

        $client = new Client($config);
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers('1500000000');
        $sendSms->setSignName('叶子坑');
        $sendSms->setTemplateCode('SMS_77670013');
        $sendSms->setTemplateParam(['code' => rand(100000, 999999)]);
        $sendSms->setOutId('demo');

        print_r($client->execute($sendSms));
    }
}