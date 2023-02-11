<?php

namespace app\v1\sms\action;

use app\v1\log\model\LogSmsModel;
use app\v1\sms\struct\SendStdErr;
use LCSms\Send;

//jj项目
class LcAction
{
    public static function SendText($type, $tag, $mch_id, $key, $phone, $text, $sign, $tpcode = null): SendStdErr
    {

        try {
            $ret = Send::full_text($mch_id, $key, $phone, $text, $sign);
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
        } catch (\Throwable $e) {
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

    public static function SendCode($type, $tag, $mch_id, $key, $phone, $text, $sign, $tpcode): SendStdErr
    {

        try {
            $ret = Send::code($mch_id, $key, $phone, $text, $sign, $tpcode);
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
        } catch (\Throwable $e) {
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