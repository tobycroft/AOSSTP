<?php

namespace app\v1\sms\action;

use app\v1\sms\model\SmsAliyunModel;
use app\v1\sms\struct\SendStdErr;

class SendAction
{

    //AutoSend:返回错误
    public static function AutoSend($proc, $phone, $param): SendStdErr|null
    {
        switch ($proc["sms_type"]) {
            case "aliyun":
                $aliyun = SmsAliyunModel::where("tag", $proc["sms_tag"])->findOrEmpty();
                if ($aliyun) {
                    return AliyunAction::Send($proc['sms_type'], $proc['sms_tag'], $aliyun['accessid'], $aliyun['accesskey'], $phone, $param, $aliyun['sign'], $aliyun['tpcode']);
                }
                break;

            case "tencent":
                \Ret::Fail(408, null, '1');
                break;

            case "ihuyi":
                \Ret::Fail(408, null, '2');
                break;

            case "zz253":
                \Ret::Fail(408, null, '3');
                break;

            case "lc":

            break;

            default:
                \Ret::Fail(408, null, "项目没有对应的短信方案或模板");
                break;
        }
        return null;
    }
}