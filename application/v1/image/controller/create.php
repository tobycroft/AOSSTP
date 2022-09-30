<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use PHPImageWorkshop\ImageWorkshop;
use think\Request;

class create extends CommonController
{

    private string $font = "../public/static/misans/misans.ttf";
    private int $font_size = 16;
    private string $font_color = "000000";
    public mixed $token;

    protected int $width;
    protected int $height;

    public function initialize()
    {
        set_time_limit(0);
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
    }

    public function index(Request $request)
    {
        if (!$request->has("width")) {
            \Ret::fail("width");
        }
        if (!$request->has("height")) {
            \Ret::fail("height");
        }
        $json = $request->post("data");
        $conf = json_decode($json, 1);
        $this->width = input("width");
        $this->height = input("height");

        $document = ImageWorkshop::initVirginLayer(1920, 1080);
        $layer1 = ImageWorkshop::initTextLayer("123", $this->font, $this->font_size, $this->font_color);
        $img = ImageWorkshop::initFromPath("https://static.familyeducation.org.cn/ps/20220927/d1831a5f5af38d56ee0f414ff849e8aa.png");
        $document->addLayer(1, $layer1, 10, 10);
        $document->addLayer(1, $img, 30, 40);
        $image = $document->getResult("ffffff");

//        header('Content-type: image/jpeg');

        imagejpeg($image, null, 95);

        \think\facade\Response::contentType("image/png")->send();
    }

}