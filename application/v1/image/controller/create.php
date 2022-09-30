<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use PHPImageWorkshop\ImageWorkshop;

class create extends CommonController
{

    public function initialize()
    {

    }

    public function index()
    {
        $document = ImageWorkshop::initVirginLayer(200, 400);

        $image = $document->getResult("ffffff");

        header('Content-type: image/jpeg');

        imagejpeg($image, null, 95);

//        \think\facade\Response::contentType("image/jpeg")->send();
    }

}