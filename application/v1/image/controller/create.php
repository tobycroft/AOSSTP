<?php

namespace app\v1\image\controller;


use think\Request;

class create
{

    public function index(Request $request)
    {
        header("Content-Type: image/jpg");
        $img = \Intervention\Image\Facades\Image::canvas(1080, 1920);
        return $img;
    }

    public function create(Request $request)
    {
    }
}