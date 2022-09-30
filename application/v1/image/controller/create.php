<?php

namespace app\v1\image\controller;


use sezaicetin\Create\img;

class create
{

    public function index()
    {
        header("Content-type:image/png");
        $img = new img("test");
        $img->create(200, 200, "../public/img");
        header("Content-type:image/png");
    }

}