<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatModel;
use BaseController\CommonController;
use Yingou\MiniProgram\MiniProgram;

class index extends CommonController
{

    public $app;
    public string $token;
    public array $config;

    public function initialize()
    {
        header("content-type: image/jpeg");
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
        header("content-type: image/jpeg");
        header("asd: dsad");

    }

    public function qrcode()
    {
        $data = input('get.data');
        $prog = new MiniProgram(new ProgramConfig($this->config));
        $ret = $prog->createQrCode->create("/test?", 480);
        header("content-type: image/jpeg");
        return $ret;
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