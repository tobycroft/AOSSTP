<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatModel;
use Wechat\Miniprogram;

class index
{

    public $app;
    public string $token;
    public array $config;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *", true);
        header("Access-Control-Max-Age: 86400", true);
        header("Access-Control-Allow-Credentials: true", true);
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE", true);
        header("Access-Control-Allow-Headers: *", true);

        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
        $wechat = WechatModel::where("project", $this->token)->find();
        if (!$wechat) {
            \Ret::fail("未找到项目");
        }
        $this->config = [
            'appid' => $wechat["appid"],
            'appsecret' => $wechat["appsecret"],
        ];
    }

    public function qrcode()
    {
//        $data = input('get.data');
//        $prog = new MiniProgram(new ProgramConfig($this->config));
//        $ret = $prog->createQrCode->create("/test?", 480);
//        return Response::create($ret, null, null, ["Content-Type" => "image/jpeg"]);
        Miniprogram::getAccessToken($this->config["appid"], $this->config["appsecret"]);
    }
}
