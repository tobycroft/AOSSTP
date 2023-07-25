<?php

namespace app\v1\sms\action;

use app\v1\log\model\LogSmsModel;
use app\v1\sms\struct\SendStdErr;
use Throwable;
use WlwxSMS\Send;

//jj-proj
class WlwxAction
{
    public static function SendText($ip, $type, $tag, $password, $cust_code, $content, $destMobiles): SendStdErr
    {

        try {
            $ret = Send::full_text($password, $cust_code, $content, $destMobiles);
            $success = false;
            //1012 ins balance
            if (strtolower($ret["respCode"]) == '0') {
                $success = true;
            }

            LogSmsModel::create([
                'oss_type' => $type,
                'oss_tag' => $tag,
                'phone' => $destMobiles,
                'text' => $content,
                'raw' => json_encode($ret, 320),
                'ip' => $ip,
                'log' => $ret['msg'],
                'success' => $success,
                'error' => false,
            ]);
            if ($success) {
                return new SendStdErr(0, null, $ret['msg']);
            } else {
                return new SendStdErr(200, $ret, $ret['msg']);

            }
        } catch (Throwable $e) {
            LogSmsModel::create(["oss_type" => $type,
                "oss_tag" => $tag,
                "phone" => $destMobiles,
                "text" => $content,
                'ip' => $ip,
                "log" => $e->getMessage(),
                "raw" => $e->getTraceAsString(),
                'success' => false,
                'error' => true,]);
            return new SendStdErr(500, null, $e->getMessage());
        }
    }

    public static function SendCode($reverse_addr, $type, $tag, $mch_id, $key, $phone, array|string $text, string $sign, $tpcode): SendStdErr
    {

        try {
            $ret = Send::code($reverse_addr, $mch_id, $key, $phone, $text, $sign, $tpcode);
            $success = false;
            if (strtolower($ret["code"]) == '00000') {
                $success = true;
            }

            LogSmsModel::create([
                'oss_type' => $type,
                'oss_tag' => $tag,
                'phone' => $phone,
                'text' => $text,
                'raw' => json_encode($ret, 320),
                'log' => $ret['msg'],
                'success' => $success,
                'error' => false,
            ]);
            if ($success) {
                return new SendStdErr(0, null, $ret['msg']);
            } else {
                return new SendStdErr(200, null, $ret['msg']);
            }
        } catch (Throwable $e) {
            LogSmsModel::create(["oss_type" => $type,
                "oss_tag" => $tag,
                "phone" => $phone,
                "text" => $text,
                "log" => $e->getMessage(),
                "raw" => $e->getTraceAsString(),
                'success' => false,
                'error' => true,]);
            return new SendStdErr(500, null, $e->getMessage());
        }
    }

}