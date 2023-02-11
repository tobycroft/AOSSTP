<?php

namespace app\v1\sms\action;

use app\v1\sms\model\SmsAliyunModel;
use app\v1\sms\model\SmsLcModel;
use app\v1\sms\struct\SendStdErr;

class SendAction
{

    //AutoSend:返回错误
    public static function AutoSend($proc, $phone, $param): SendStdErr|null
    {
        switch ($proc["sms_type"]) {
            case "aliyun":
                $data = SmsAliyunModel::where("tag", $proc["sms_tag"])->findOrEmpty();
                if ($data) {
                    return AliyunAction::Send($proc['sms_type'], $proc['sms_tag'], $data['accessid'], $data['accesskey'], $phone, $param, $data['tpcode'], $data['sign']);
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
                $data = SmsLcModel::where('tag', $proc['sms_tag'])->findOrEmpty();
                if ($data) {
                    return LcAction::SendText($proc['sms_type'], $proc['sms_tag'], $data['mch_id'], $data['key'], $phone, $param, $data['sign'], $data['tpcode']);
                }
                break;

            default:
                \Ret::Fail(408, null, "项目没有对应的短信方案或模板");
                break;
        }
        return null;
    }
}