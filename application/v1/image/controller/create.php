<?php

namespace app\v1\image\controller;


use BaseController\CommonController;
use PHPImageWorkshop\ImageWorkshop;
use think\Request;

class create extends CommonController
{


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

    public function create(Request $request)
    {
        if (!$request->has("width")) {
            \Ret::fail("width");
        }
        if (!$request->has("height")) {
            \Ret::fail("height");
        }
        $this->width = input("width");
        $this->height = input("height");
        $json = $request->post("data");
        $data = json_decode($json, 1);
        $document = ImageWorkshop::initVirginLayer($this->width, $this->height);

        foreach ($data as $item) {
            $layer_class = new \DataAction();
            $layer = $layer_class->handle($item);
            if (!$layer) {
                \Ret::fail("数据没有准备好");
            }
            in_array($layer_class, $haystack)
            $document->addLayer(1, $layer, $layer_class->x, $layer_class->y, $align);
        }


        $document->addLayer(1, $img, 30, 40);
        $image = $document->getResult("ffffff");
        $document->delete();

//        header('Content-type: image/jpeg');

        imagejpeg($image, null, 95);

        \think\facade\Response::contentType("image/png")->send();
    }

}