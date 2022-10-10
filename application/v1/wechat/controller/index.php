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
            'access_token' => $wechat["access_token"],
        ];
        $expire_after = strtotime($wechat["expire_after"]);
        if ($expire_after < time()) {
            $data = Miniprogram::getAccessToken($this->config["appid"], $this->config["appsecret"]);
            $this->config["access_token"] = $data->access_token;
            WechatModel::where("project", $this->token)->data(
                [
                    "access_token" => $data->access_token,
                    "expire_after" => date("Y-m-d H:i:s", $data->expires_in + time() - 600)
                ]
            )->update();
        }
    }

    public function qrcode()
    {
        $data = input('data');

    }
}
