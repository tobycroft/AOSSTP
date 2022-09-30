<?php

namespace app\v1\image\controller;



class create
{

    public function index()
    {
//        header("Content-Type: image/jpg");
        $img = \Intervention\Image\Facades\Image::canvas(800, 600);
        $img->response();
    }

}