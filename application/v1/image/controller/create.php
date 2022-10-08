<?php

namespace app\v1\image\controller;


use app\v1\image\action\DataAction;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use OSS\Core\OssException;
use PHPImageWorkshop\ImageWorkshop;
use SendFile\SendFile;
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
        $document->getResult($this->background);
        $document->save("../public/upload/image/" . $this->token, $md5 . ".jpg");
        $path_name = "../public/upload/image/" . $this->token . "/" . $md5 . ".jpg";
        $fileName = "/image/" . $this->token . "/" . $md5 . ".jpg";

        if ($this->proc["type"] == "local" || $this->proc["type"] == "all") {
            if ($this->proc['main_type'] == 'local') {
                $sav = $this->proc['url'] . "/image/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
            }
        }
        if ($this->proc["type"] == "dp" || $this->proc["type"] == "all") {
            $sf = new SendFile();
            $ret = $sf->send('http://' . $this->proc["endpoint"] . '/up?token=' . $this->proc["bucket"], realpath('./upload/' . $fileName), "image/jpg", $md5 . "jpg");
            $json = json_decode($ret, 1);
            $sav = $this->proc['url'] . '/' . $json["data"];
        }
        if ($this->proc["type"] == "oss" || $this->proc["type"] == "all") {
            try {
                $oss = new \OSS\AliyunOSS($this->proc);
                $ret = $oss->uploadFile($this->proc['bucket'], $fileName, $path_name);
            } catch (OssException $e) {
                \Ret::fail($e->getMessage(), 200);
            }
            if (empty($ret->getData()["info"]["url"])) {
                \Ret::fail("OSS不正常");
            }
            if ($this->proc['main_type'] == 'oss') {
                $sav = $this->proc['url'] . '/' . $fileName;
            }
            if ($this->proc["type"] != "all") {
                $document->delete();
                unlink($path_name);
            }
        }
        \Ret::succ($sav);
    }

}