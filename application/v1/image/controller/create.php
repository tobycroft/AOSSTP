<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use sezaicetin\Create\img;

class create extends CommonController
{

    public function initialize()
    {
        header("Content-type:image/png");

    }

    public function index()
    {
        $img = new img("test");
        $img->create(200, 200);
        header("Content-type:image/png");
    }

}