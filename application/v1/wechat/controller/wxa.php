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
            \Ret::Fail(404, null, "未找到项目");
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
            \Ret::Fail(400, null, 'data');
        }
        if (!$request->has("page")) {
            \Ret::Fail(400, null, "page");
        }
        $data = input('data');
        $page = input("page");
        $md5 = md5($data);

        $wechat_data = WechatDataModel::where('key', $md5)->where('project', $this->token)->where('page', $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($this->path_prefix . $wechat_data["path"])) {
//                \Ret::succ($this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg");
//                echo file_get_contents($this->path_prefix . $wechat_data["path"]);
                $this->redirect($this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg", 302);
//                Response::contentType("image/jpg")->send();
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $md5, $page, 400);
        $real_path = $this->path_prefix . "wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".jpg";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            $sav = $this->oss_operation($md5, $fileName, $wxa, $data, $page, $oss_path);
//            echo $wxa->image;
            $this->redirect($sav, 302);
//            Response::contentType("image/jpg")->send();
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function unlimited_base64(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::Fail(400, null, 'data');
        }
        if (!$request->has("page")) {
            \Ret::Fail(400, null, "page");
        }
        $data = input('data');
        $page = input("page");
        $md5 = md5($data);

        $wechat_data = WechatDataModel::where('key', $md5)->where('project', $this->token)->where('page', $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($this->path_prefix . $wechat_data["path"])) {
                \Ret::Success(0, base64_encode(file_get_contents($this->path_prefix . $wechat_data["path"])), "from_cache");
                return;
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $md5, $page, 400);
        $real_path = $this->path_prefix . "wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".jpg";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
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
            \Ret::Success(0, base64_encode($wxa->image));
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function unlimited_file(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::Fail(400, null, 'data');
        }
        if (!$request->has("page")) {
            \Ret::Fail(400, null, "page");
        }
        $data = input('data');
        $page = input("page");
        $md5 = md5($data);

        $wechat_data = WechatDataModel::where("key", $md5)->where("project", $this->token)->where("page", $page)->find();
        if (!empty($wechat_data)) {
            if (file_exists($this->path_prefix . $wechat_data["path"])) {
                \Ret::Success(0, $this->proc['url'] . $wechat_data["path"], "from_cache");
            }
        }
        $wxa = Miniprogram::getWxaCodeUnlimit($this->access_token, $md5, $page, 400);
        $real_path = $this->path_prefix . "wechat/" . $this->token;
        $fileName = $real_path . DIRECTORY_SEPARATOR . $md5 . ".jpg";
        $oss_path = "wechat/" . $this->token . DIRECTORY_SEPARATOR . $md5 . ".jpg";
        if (!is_dir($real_path)) {
            mkdir($real_path, 0755, true);
        }
        if ($wxa->isSuccess()) {
            $sav = $this->oss_operation($md5, $fileName, $wxa, $data, $page, $oss_path);
            \Ret::Success(0, $sav);
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
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
            \Ret::Fail(400, null, "文件写入失败");
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
                if (empty($ret->getData()['info']['url'])) {
                    \Ret::Fail(300, null, 'OSS不正常');
                    return '';
                }
            } catch (OssException $e) {
                \Ret::Fail(200, null, $e->getMessage());
                return "";
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
            \Ret::Success(0, $data);
        } else {
            \Ret::Fail(404);
        }
    }

    public function scheme()
    {
        $scheme = input("scheme");
        $data = WechatDataModel::where("project", $this->token)->where("key", $scheme)->find();
        if ($data) {
            $data["url"] = $this->proc['url'] . "/wechat/" . $this->token . DIRECTORY_SEPARATOR . $data["key"] . ".jpg";
            \Ret::Success(0, $data);
        } else {
            \Ret::Fail(404);
        }
    }

    public function getuserphonenumber(Request $request)
    {
        if (!$request->has('code')) {
            \Ret::Fail(400, null, 'code');
        }
        $code = input('code');

        $wxa = Miniprogram::getuserphonenumber($this->access_token, $code);
        if ($wxa->isSuccess()) {
            \Ret::Success(0, [
                'phoneNumber' => $wxa->phoneNumber,
                'purePhoneNumber' => $wxa->purePhoneNumber,
                'countryCode' => $wxa->countryCode,
                'watermark' => $wxa->watermark,
            ]);
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

    public function generatescheme(Request $request)
    {
        if (!$request->has('path'))
            \Ret::Fail(400, null, 'path');
        if (!$request->has('query'))
            \Ret::Fail(400, null, 'query');
        if (!$request->has('is_expire'))
            \Ret::Fail(400, null, 'is_expire');
        if (!$request->has('expire_interval'))
            \Ret::Fail(400, null, 'expire_interval');

        $path = input('path');
        $query = input('query');
        $is_expire = input('is_expire');
        $expire_interval = input('expire_interval');

        $wxa = Miniprogram::generatescheme($this->access_token, $path, $query, $is_expire, $expire_interval);
        if ($wxa->isSuccess()) {
            \Ret::Success(0, [
                'openlink' => $wxa->openlink,
            ]);
        } else {
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }
}
