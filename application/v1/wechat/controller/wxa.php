<?php

namespace app\v1\wechat\controller;

use app\v1\image\controller\create;
use app\v1\wechat\model\WechatDataModel;
use app\v1\wechat\model\WechatModel;
use OSS\AliyunOSS;
use OSS\Core\OssException;
use SendFile\SendFile;
use think\facade\Response;
use think\Request;
use Wechat\Miniprogram;

class wxa extends create
{

    public $app;
    public mixed $access_token;
    public string $appid;
    public string $appsecret;

    public function initialize()
    {
        parent::initialize();

        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
        $wechat = WechatModel::where("project", $this->token)->find();
        if (!$wechat) {
            \Ret::fail("未找到项目");
        }
        $this->appid = $wechat["appid"];
        $this->appsecret = $wechat["appsecret"];
        $this->access_token = $wechat["access_token"];

        $expire_after = strtotime($wechat["expire_after"]);
        if ($expire_after < time() || empty($wechat["access_token"])) {
            $data = Miniprogram::getAccessToken($this->appid, $this->appsecret);
            if ($data->isSuccess()) {
                $this->access_token = $data->access_token;
                WechatModel::where("project", $this->token)->data(
                    [
                        "access_token" => $data->access_token,
                        "expire_after" => date("Y-m-d H:i:s", $data->expires_in + time() - 600)
                    ]
                )->update();
            } else {
                echo $data->error();
                exit();
            }
        }
    }

    public function unlimited_raw(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        if (!$request->has("page")) {
            \Ret::fail("page");
        }
        $data = input('data');
        $page = input("page");
        $md5 = md5($data . '|' . $page);
        $wechat_data = WechatDataModel::where("key", $md5)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($wechat_data["path"])) {
                echo file_get_contents($wechat_data["path"]);
                Response::contentType("image/png")->send();
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $data, $page, 400);
        $real_path = "../public/upload/wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".png";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".png";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            if (file_put_contents($fileName, $wxa)) {
                WechatDataModel::create([
                    "key" => $md5,
                    "val" => $data,
                    "page" => $page,
                    "path" => $oss_path
                ]);
            }
            echo $wxa->image;
            Response::contentType("image/png")->send();
        } else {
            \Ret::fail($wxa->error());
        }
    }

    public function unlimited_base64(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        if (!$request->has("page")) {
            \Ret::fail("page");
        }
        $data = input('data');
        $page = input("page");
        $md5 = md5($data . '|' . $page);
        $wechat_data = WechatDataModel::where("key", $md5)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($wechat_data["path"])) {
                \Ret::succ(base64_encode(file_get_contents($wechat_data["path"])));
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $data, $page, 400);
        $real_path = "../public/upload/wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".png";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".png";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            if (file_put_contents($fileName, $wxa)) {
                WechatDataModel::create([
                    "key" => $md5,
                    "val" => $data,
                    "page" => $page,
                    "path" => $oss_path
                ]);
            }
            \Ret::succ(base64_encode($wxa->image));
        } else {
            \Ret::fail($wxa->error());
        }
    }

    public function unlimited(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        if (!$request->has("page")) {
            \Ret::fail("page");
        }
        $data = input('data');
        $page = input("page");
        $md5 = md5($data . '|' . $page);
        $wechat_data = WechatDataModel::where("key", $md5)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($wechat_data["path"])) {
                \Ret::succ(file_get_contents($wechat_data["path"]));
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $data, $page, 400);
        $real_path = "../public/upload/wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".png";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".png";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            if (file_put_contents($fileName, $wxa)) {
                WechatDataModel::create([
                    "key" => $md5,
                    "val" => $data,
                    "page" => $page,
                    "path" => $oss_path
                ]);
            }
            \Ret::succ($wxa->image);
        } else {
            \Ret::fail($wxa->error());
        }
    }

    public function unlimited_file(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        if (!$request->has("page")) {
            \Ret::fail("page");
        }
        $data = input('data');
        $page = input("page");
        $md5 = md5($data . '|' . $page);
        $wechat_data = WechatDataModel::where("key", $md5)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($wechat_data["path"])) {
                \Ret::succ(base64_encode(file_get_contents($wechat_data["path"])));
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $data, $page, 400);
        $real_path = "../public/upload/wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".png";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".png";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            if (file_put_contents($fileName, $wxa)) {
                WechatDataModel::create([
                    "key" => $md5,
                    "val" => $data,
                    "page" => $page,
                    "path" => $oss_path
                ]);
            }
            if ($this->proc["type"] == "local" || $this->proc["type"] == "all") {
                if ($this->proc['main_type'] == 'local') {
                    $sav = $this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
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
                    $oss = new AliyunOSS($this->proc);
                    $ret = $oss->uploadFile($this->proc['bucket'], $md5 . ".png", $fileName);
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
                    unlink($fileName);
                }
            }
            \Ret::succ($sav);
        } else {
            \Ret::fail($wxa->error());
        }
    }
}
