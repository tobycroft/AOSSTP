<?php

namespace app\v1\wechat\controller;

use app\v1\image\controller\create;
use app\v1\wechat\model\WechatDataModel;
use app\v1\wechat\model\WechatModel;
use OSS\AliyunOSS;
use OSS\Core\OssException;
use think\Request;
use Wechat\Miniprogram;
use Wechat\WechatRet\WxaCode\GetUnlimited;

class wxa extends create
{

    public mixed $access_token;
    public string $appid;
    public string $appsecret;
    public string $path_prefix = "./upload/";

    public function initialize()
    {
        parent::initialize();
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
        $md5 = md5($data);

        $wechat_data = WechatDataModel::where("key", $md5)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($this->path_prefix . $wechat_data["path"])) {
//                \Ret::succ($this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg");
//                echo file_get_contents($this->path_prefix . $wechat_data["path"]);
                $this->redirect($this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg", 302);
//                Response::contentType("image/png")->send();
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $md5, $page, 400);
        $real_path = $this->path_prefix . "wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".png";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".png";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            $sav = $this->oss_operation($md5, $fileName, $wxa, $data, $page, $oss_path);
//            echo $wxa->image;
            $this->redirect($sav, 302);
//            Response::contentType("image/png")->send();
        } else {
            \Ret::fail($wxa->getError());
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
        $md5 = md5($data);

        $wechat_data = WechatDataModel::where("key", $md5)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($this->path_prefix . $wechat_data["path"])) {
                \Ret::succ(base64_encode(file_get_contents($this->path_prefix . $wechat_data["path"])));
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $md5, $page, 400);
        $real_path = $this->path_prefix . "wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".png";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".png";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            if (file_put_contents($fileName, $wxa->image)) {
                $sav = $this->oss_operation($md5, $fileName, $wxa, $data, $page, $oss_path);
                WechatDataModel::where("project", $this->token)->where("key", $md5)->delete();
                WechatDataModel::create([
                    "project" => $this->token,
                    "key" => $md5,
                    "val" => $data,
                    "page" => $page,
                    "path" => $oss_path
                ]);
            }
            \Ret::succ(base64_encode($wxa->image));
        } else {
            \Ret::fail($wxa->getError());
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
        $md5 = md5($data);

        $wechat_data = WechatDataModel::where("key", $md5)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($this->path_prefix . $wechat_data["path"])) {
                \Ret::succ($this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg");
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $md5, $page, 400);
        $real_path = $this->path_prefix . "wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".png";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".png";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            $sav = $this->oss_operation($md5, $fileName, $wxa, $data, $page, $oss_path);
            \Ret::succ($sav);
        } else {
            \Ret::fail($wxa->getError());
        }
    }

    /**
     * @param string $md5
     * @param string $fileName
     * @param GetUnlimited $wxa
     * @param mixed $data
     * @param mixed $page
     * @param string $oss_path
     * @return string
     */
    protected function oss_operation(string $md5, string $fileName, GetUnlimited $wxa, mixed $data, mixed $page, string $oss_path): string
    {
        if (!file_put_contents($fileName, $wxa->image)) {
            \Ret::fail("文件写入失败");
        }
        if ($this->proc["type"] == "local" || $this->proc["type"] == "all") {
            if ($this->proc['main_type'] == 'local') {
                $sav = $this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
            }
        }
        if ($this->proc["type"] == "oss" || $this->proc["type"] == "all") {
            try {
                $oss = new AliyunOSS($this->proc);
                $ret = $oss->uploadFile($this->proc['bucket'], "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg", $fileName);
            } catch (OssException $e) {
                \Ret::fail($e->getMessage(), 200);
            }
            if (empty($ret->getData()["info"]["url"])) {
                \Ret::fail("OSS不正常");
            }
            if ($this->proc['main_type'] == 'oss') {
                $sav = $this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
            }
        }
        WechatDataModel::where("project", $this->token)->where("key", $md5)->delete();
        WechatDataModel::create([
            "project" => $this->token,
            "key" => $md5,
            "val" => $data,
            "page" => $page,
            "path" => $oss_path
        ]);
        return $sav;
    }

    public function scene()
    {
        $scene = input("scene");
        $data = WechatDataModel::where("project", $this->token)->where("key", $scene)->find();
        if ($data) {
            $data["url"] = $this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $data["key"] . ".jpg";
            \Ret::succ($data);
        } else {
            \Ret::fail([], 404);
        }
    }

    public function scheme()
    {
        $scheme = input("scheme");
        $data = WechatDataModel::where("project", $this->token)->where("key", $scheme)->find();
        if ($data) {
            $data["url"] = $this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $data["key"] . ".jpg";
            \Ret::succ($data);
        } else {
            \Ret::fail([], 404);
        }
    }

    public function getuserphonenumber(Request $request)
    {
        if (!$request->has('code')) {
            \Ret::fail('code');
        }
        $code = input('code');

        $wxa = Miniprogram::getuserphonenumber($this->access_token, $code);
        if ($wxa->isSuccess()) {
            \Ret::succ([
                'phoneNumber' => $wxa->phoneNumber,
                'purePhoneNumber' => $wxa->purePhoneNumber,
                'countryCode' => $wxa->countryCode,
                'watermark' => $wxa->watermark,
            ]);
        } else {
            \Ret::fail($wxa->getError());
        }
    }
}
