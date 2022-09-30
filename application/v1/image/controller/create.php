<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use PHPImageWorkshop\ImageWorkshop;

class create extends CommonController
{
    private string $font = "../public/static/misans/misans.ttf";
    private int $font_size = 16;
    private string $font_color = "000000";
    public $token;

    public function initialize()
    {
        set_time_limit(0);
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
    }


    public function index()
    {
        $document = ImageWorkshop::initVirginLayer(200, 400);
        $layer1 = ImageWorkshop::initTextLayer("123", $this->font, $this->font_size, $this->font_color);
        $document->addLayer(1, $layer1, 0, 0);
        $image = $document->getResult("ffffff");

//        header('Content-type: image/jpeg');

        imagejpeg($image, null, 95);

        \think\facade\Response::contentType("image/png")->send();
    }

}