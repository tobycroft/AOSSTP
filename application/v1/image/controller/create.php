<?php

namespace app\v1\image\controller;



use sezaicetin\Create\img;

class create
{

    public function index()
    {
//        header("Content-Type: image/jpg");
        $img = new img("test");
        $img->create(200, 200);
    }

}