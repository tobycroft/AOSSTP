<?php

namespace app\v1\wechat\controller;

class api
{

    public static function recv()
    {
        var_dump(request()->input());
//        $in = \Input::Raw();
//        WechatMessage::create([
//            "raw" => $in
//        ]);
    }
}