<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use sezaicetin\Create\img;

class create extends CommonController
{

    public function initialize()
    {

    }

    public function index()
    {
        header("Content-type", "image/png");
        $img = new img("test");
        $img->create(200, 200);
        \think\facade\Response::contentType("Content-type", "image/jpeg")->getContent();
    }

}