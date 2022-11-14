<?php

namespace app\v1\wechat\controller;

use app\v1\image\controller\create;
use app\v1\wechat\model\WechatModel;
use think\Request;
use Wechat\Miniprogram;

class info extends create
{
    public mixed $access_token;

    public function auto_accesskey(Request $request)
    {
        \Ret::Success(0, [
            "address" => Miniprogram::getBase() . Miniprogram::getAccessTokenPath(),

        ]);
    }


    public function set_accesstoken(Request $request)
    {
        if (!$access_token = input("access_token")) {
            \Ret::Fail(400, null, "access_token");
        }
        if (!$expires_in = input("expires_in")) {
            \Ret::Fail(400, null, "expires_in");
        }

        $this->access_token = $access_token;
        if (WechatModel::where('project', $this->token)->data(
            [
                'access_token' => $this->access_token,
                'expire_after' => date('Y-m-d H:i:s', $expires_in + time() - 600)
            ]
        )->update()) {
            \Ret::Success(0);
        } else {
            \Ret::Fail(500);
        }
    }


}