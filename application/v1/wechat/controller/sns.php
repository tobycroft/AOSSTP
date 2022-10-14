<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatModel;
use BaseController\CommonController;
use think\Request;
use Wechat\Miniprogram;

class sns extends CommonController
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

    public function jscode(Request $request)
    {
        if (!$request->has("js_code")) {
            \Ret::fail("js_code");
        }
        $js_code = input('js_code');

        $wxa = Miniprogram::jscode2session($this->appid, $this->appsecret, $js_code, "authorization_code");
        if ($wxa->isSuccess()) {
            \Ret::succ([
                "openid" => $wxa->openid,
                "unionid" => $wxa->unionid,
                "session_key" => $wxa->session_key,
            ]);
        } else {
            \Ret::fail($wxa->getError());
        }
    }

}
