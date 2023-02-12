<?php

namespace app\v1\wechat\controller;

use app\v1\log\model\LogWebModel;

class api
{

    public static function recv()
    {
        $in = \Input::Raw();
        LogWebModel::create([
            "get" => json_encode(request()->get()),
            "post" => json_encode(request()->post()),
            "raw" => json_encode(request()->getInput()),
            "header" => json_encode(request()->header()),
        ]);
    }
}