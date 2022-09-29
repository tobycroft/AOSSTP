<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use CreateImg\InfoCodePhoto;
use think\Request;

class create extends CommonController
{

    public function index(Request $request)
    {
        $a = new InfoCodePhoto();
    }
}