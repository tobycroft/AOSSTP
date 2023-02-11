<?php

namespace app\v1\sms\action;

use app\v1\sms\model\SmsAliyunModel;

class SendAction
{
    public static function AutoSend($proc, $phone, $param): bool
    {
        switch ($proc["sms_type"]) {
            case "aliyun":
                $aliyun = SmsAliyunModel::where("tag", $proc["sms_tag"])->findOrEmpty();
                if ($aliyun) {
                    var_dump(AliyunAction::Send($proc['sms_type'], $proc['sms_tag'], $aliyun['accessid'], $aliyun['accesskey'], $aliyun['sign'], $aliyun['tpcode'], $phone, $param));
                }

                break;

            case
            "tencent":

                break;

            case "ihuyi":
                break;

            case "zz253":
                break;

            case "lc":
                break;

            default:
                \Ret::Fail(408, null, "项目没有对应的短信方案或模板");
                break;
        }
    }
}