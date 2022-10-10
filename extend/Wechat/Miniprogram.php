<?php

namespace Wechat;

use function Wechat\WechatRet\raw_post;

class GetAccessToken extends WechatUrl
{
    protected static $Base = "https://api.weixin.qq.com";

    public function getAccessToken(string $appid, $secret, $grant_type = "client_credential")
    {
        return new \Wechat\WechatRet\GetAccessToken(raw_post(self::$Base . self::$getAccessToken, [
            "appid" => $appid,
            "secret" => $secret,
            "grant_type" => $grant_type,
        ]));
    }
}