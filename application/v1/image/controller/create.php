<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use CreateImg\InfoCodePhoto;

class create extends CommonController
{

    public function index(Request $request)
    {
        $a = new InfoCodePhoto();
    }
}