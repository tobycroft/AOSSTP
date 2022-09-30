<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use Devbr\Canvas;

class create extends CommonController
{

    public function initialize()
    {

    }

    public function index()
    {
        $img = new Canvas();
        $img->create_empty_image(200, 400, "png")
            ->set_rgb('#df0d32')
//            ->merge("test_image.png", array("right", "bottom"))
            ->filter("blur", 23)
            ->show();
//        \think\facade\Response::contentType("image/jpeg")->send();
    }

}