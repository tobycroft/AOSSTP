<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use sezaicetin\Create\img;

class create extends CommonController
{

    public function initialize()
    {
        header("Content-type:image/png", true);

    }

    public function index()
    {
        echo get_headers();
        $img = new img("test");
        $img->create(200, 200);
    }

}