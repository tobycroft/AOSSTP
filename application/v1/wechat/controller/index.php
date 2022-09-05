<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatModel;
use BaseController\CommonController;
use WeMini\Crypt;

class index extends CommonController
{

    public $app;
    public string $token;
    public array $config;

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
        $this->config = [
            'appid' => $wechat["app_id"],
            'appsecret' => $wechat["secret"],
            'token' => $wechat["token"],
            'encodingaeskey' => $wechat["aes_key"]
        ];

    }

    public function qrcode()
    {
        $data = input('get.data');
        $this->app = Crypt::instance();
        $ret = $this->app->getClient()->postJson("wxa/getwxacodeunlimit", [
            'scene' => '123',
            'page' => 'pages/index/index',
            'width' => 430,
            'check_path' => false,
        ]);
        $path = $ret->saveAs();
    }
}