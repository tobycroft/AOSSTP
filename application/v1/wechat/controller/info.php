<?php

namespace app\v1\wechat\controller;

use app\v1\image\controller\create;
use app\v1\wechat\model\WechatModel;
use think\Request;
use Wechat\Miniprogram;
use Wechat\WechatRet\GetAccessToken;

class info extends create
{

    public string $appid;
    public string $appsecret;
    public mixed $access_token;

    public mixed $wechat;


    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->wechat = WechatModel::where('project', $this->token)->find();
        if (!$this->wechat) {
            \Ret::Fail(404, null, '未找到项目');
        }
        $this->appid = $this->wechat['appid'];
        $this->appsecret = $this->wechat['appsecret'];
        $this->access_token = $this->wechat['access_token'];
    }

    public function get_accesstoken(Request $request)
    {
        \Ret::Success(0, [
            "address" => Miniprogram::getBase() . Miniprogram::getAccessTokenPath(),
            "postdata" => [
                'appid' => $this->appid,
                'secret' => $this->appsecret,
                'grant_type' => "client_credential",
            ]
        ]);
    }


    public function set_accesstoken(Request $request)
    {
//        if (!$access_token = input("access_token")) {
//            \Ret::Fail(400, null, "access_token");
//        }
//        if (!$expires_in = input("expires_in")) {
//            \Ret::Fail(400, null, "expires_in");
//        }
        if (!$data = input("data")) {
            \Ret::Fail(400, null, "data");
        }
        $getak = new GetAccessToken($data);
        $this->access_token = $getak->access_token;
        if (WechatModel::where('project', $this->token)->data(
            [
                'access_token' => $getak->access_token,
                'expire_after' => date('Y-m-d H:i:s', $getak->expires_in + time() - 600)
            ]
        )->update()) {
            \Ret::Success(0);
        } else {
            \Ret::Fail(500);
        }
    }


}