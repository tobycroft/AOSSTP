<?php

namespace app\v1\sms\action;

class SendAction
{
    public static function AutoSend($proc)
    {
        switch ($proc["sms_type"]) {
            case "aliyun":

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