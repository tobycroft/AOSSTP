<?php

namespace app\v1\wechat\controller;

use think\Request;
use Wechat\Miniprogram;

class sns extends wxa
{


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
