<?php

namespace app\v1\image\controller;


use Intervention\Image\Facades\Image;
use think\Request;

class create
{

    public function index(Request $request)
    {
//        header("Content-Type: image/jpg");
        $img = Image::canvas(1080, 1920);
        return $img;
    }

    public function create(Request $request)
    {
    }
}