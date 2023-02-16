<?php

namespace app\v1\tasker\controller;

use app\v1\log\model\LogWebModel;
use Input;

class log
{
    public function upload()
    {
        $in = Input::Raw();
        LogWebModel::create([
            'get' => json_encode(request()->get()),
            'post' => json_encode(request()->post()),
            'raw' => $in,
            'header' => json_encode(request()->header()),
            'method' => request()->method(),
        ]);
        echo 123;
    }
}