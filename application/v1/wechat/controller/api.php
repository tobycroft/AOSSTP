<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatMessage;

class api
{

    public static function recv()
    {
        $in = \Input::Raw();
        WechatMessage::create([
            "raw" => $in
        ]);
    }
}