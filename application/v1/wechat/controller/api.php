<?php

namespace app\v1\wechat\controller;

class api
{

    public static function recv()
    {
//        $in = \Input::Raw();
        WechatMessage::create([
            "raw" => json_encode(request()->post())
        ]);
    }
}