<?php

namespace app\v1\wechat\controller;

use think\Request;
use Wechat\Miniprogram;

class sns extends wxa
{


    public function jscode(Request $request)
    {
        if (!$request->has("js_code")) {
            \Ret::Fail(400, null, "js_code");
        }
        $js_code = input('js_code');

        $wxa = Miniprogram::jscode2session($this->appid, $this->appsecret, $js_code, "authorization_code");
        if ($wxa->isSuccess()) {
            \Ret::Success(0, [
                "openid" => $wxa->openid,
                "unionid" => $wxa->unionid,
                "session_key" => $wxa->session_key,
            ]);
        } else {
            \Ret::Fail(300, $wxa->getError());
        }
    }

    public function jscode2session(Request $request)
    {
        $this->jscode($request);
    }

}
