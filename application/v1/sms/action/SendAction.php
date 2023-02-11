<?php

namespace app\v1\sms\action;

use app\v1\sms\model\SmsAliyunModel;

class SendAction
{
    public static function AutoSend($proc)
    {
        switch ($proc["sms_type"]) {
            case "aliyun":
                $aliyun = SmsAliyunModel::where("tag", $proc["sms_tag"])->findOrEmpty();
                if ($aliyun) {
                    $aliyun["accessid"];
                    $aliyun["accesskey"];
                    $aliyun["sign"];
                    $aliyun["tpcode"];
                    AliyunAction::Send();
                }
                break;

            case "tencent":
                break;

            case "ihuyi":
                break;

            case "zz253":
                break;

            case "lc":
                break;
            default:
                break;
        }
    }
}