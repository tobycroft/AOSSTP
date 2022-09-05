<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatModel;
use Yingou\MiniProgram\MiniProgram;

class index
{

    public $app;
    public string $token;
    public array $config;

    public function __construct()
    {
        header("Content-Type: image/jpeg");

        header("Access-Control-Allow-Origin: *", true);
        header("Access-Control-Max-Age: 86400", true);
        header("Access-Control-Allow-Credentials: true", true);
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE", true);
        header("Access-Control-Allow-Headers: *", true);
        header("bbb: bbb");

        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
        $wechat = WechatModel::where("project", $this->token)->find();
        if (!$wechat) {
            \Ret::fail("未找到项目");
        }
        $this->config = [
            'appId' => $wechat["app_id"],
            'secret' => $wechat["secret"],
        ];
    }

    public function qrcode()
    {
        header("ccc: ccc");
        $data = input('get.data');
        $prog = new MiniProgram(new ProgramConfig($this->config));
        $ret = $prog->createQrCode->create("/test?", 480);
        header_remove("content-type");
        header("content-type: image/jpeg", true);
        var_dump(getallheaders());
//        return $ret;
    }
}

class ProgramConfig extends \Yingou\MiniProgram\Config
{

    public function getAccessToken()
    {
        if (!file_exists($this->tmpFile)) {
            return null;
        }
        $data = json_decode(file_get_contents($this->tmpFile), true);
        if ($data['expire'] > time()) {
            return $data['token'];
        }
        return null;
    }

    public function setAccessToken($token, $expires = 0)
    {
        return file_put_contents($this->tmpFile, json_encode(['token' => $token, 'expire' => (time() + $expires)]));
    }
}