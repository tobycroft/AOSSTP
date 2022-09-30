<?php

namespace app\v1\image\controller;


use app\v1\file\controller\index;
use app\v1\image\action\DataAction;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use PHPImageWorkshop\ImageWorkshop;
use think\Exception;
use think\Request;

class create extends CommonController
{


    public mixed $token;
    public mixed $proc;
    protected int $width;
    protected int $height;
    protected string $background;

    public function initialize()
    {
        set_time_limit(0);
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            \Ret::fail('项目不可用');
        }
    }

    public function canvas(Request $request)
    {
        if (!$request->has("width")) {
            \Ret::fail("width");
        }
        if (!$request->has("height")) {
            \Ret::fail("height");
        }
        if (!$request->has("background")) {
            \Ret::fail("background");
        }
        $this->width = input("width");
        $this->height = input("height");
        $this->background = input("background");
        $json = $request->post("data");
        $data = json_decode($json, 1);
        $document = ImageWorkshop::initVirginLayer($this->width, $this->height);

        foreach ($data as $item) {
            try {
                $layer_class = new DataAction($item);
                $layer = $layer_class->handle();
                $document->addLayer(1, $layer, $layer_class->x, $layer_class->y, $layer_class->position);
            } catch (Exception $e) {
                \Ret::fail($e->getMessage());
            }
        }
        $image = $document->getResult($this->background);
        $document->delete();
        imagejpeg($image, null, 95);
        \think\facade\Response::contentType("image/png")->send();
    }

    public function file(Request $request)
    {
        if (!$request->has("width")) {
            \Ret::fail("width");
        }
        if (!$request->has("height")) {
            \Ret::fail("height");
        }
        if (!$request->has("background")) {
            \Ret::fail("background");
        }
        $this->width = input("width");
        $this->height = input("height");
        $this->background = input("background");
        $json = $request->post("data");
        $data = json_decode($json, 1);
        $document = ImageWorkshop::initVirginLayer($this->width, $this->height);

        foreach ($data as $item) {
            try {
                $layer_class = new DataAction($item);
                $layer = $layer_class->handle();
                $document->addLayer(1, $layer, $layer_class->x, $layer_class->y, $layer_class->position);
            } catch (Exception $e) {
                \Ret::fail($e->getMessage());
            }
        }
        $crypt = [
            "width" => $this->width,
            "height" => $this->height,
            "background" => $this->background,
            "data" => $data
        ];
        $md5 = md5(json_encode($crypt, 320));
        $image = $document->getResult($this->background);
        $document->save("../public/upload/image/" . $this->token, $md5 . ".jpeg");
//        $img = Image::open($image);
//        $info = $img->save("../upload/image/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg");
//        $sav = $this->proc['url'] . '/' . "upload/image/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
//        \Ret::succ($sav);
        $_FILES["file"]["name"] = $md5 . ".jpeg";
        $_FILES["file"]["error"] = 0;
        $_FILES["file"]["type"] = "image/jpeg";
        $_FILES["file"]["tmp_name"] = "../public/upload/image/" . $this->token . "/" . $md5 . ".jpeg";
        $index = new index();
        $index->upload_file($request, 1, "complete");
    }

}