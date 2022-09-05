<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatModel;
use BaseController\CommonController;
use WeMini\Crypt;
use Yingou\MiniProgram\MiniProgram;

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
            'appId' => $wechat["app_id"],
            'secret' => $wechat["secret"],
        ];

    }

    public function qrcode()
    {
        $data = input('get.data');
        $prog = new MiniProgram($this->config);
        $prog->createQrCode->create("test", 480);
    }
}