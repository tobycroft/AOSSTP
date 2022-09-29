<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use CreateImg\InfoCodePhoto;
use think\Request;

class create extends CommonController
{

    public function index(Request $request)
    {
        header("Content-Type: image/jpg");
        $a = new InfoCodePhoto();
        $a->generate_photo("title", [], './upload/qr', './upload/qr');
    }
}