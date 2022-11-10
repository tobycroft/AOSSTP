<?php

namespace Wechat;

use Wechat\WechatRet\GetAccessToken;
use Wechat\WechatRet\UserGet;
use Wechat\WechatRet\UserInfo;

class OfficialAccount extends Miniprogram
{

    public static function getAccessToken(string $appid, $secret, $grant_type = "client_credential"): GetAccessToken
    {
        return new GetAccessToken(
            raw_post(self::$Base . self::$getAccessToken,
                [
                    "appid" => $appid,
                    "secret" => $secret,
                    "grant_type" => $grant_type,
                ]
            )
        );
    }

    public static function userlist(string $access_token, $next_openid): UserGet
    {
        return new UserGet(raw_post(self::$Base . self::$user_get,
            [
                "access_token" => $access_token,
                "next_openid" => $next_openid,
            ]
        ));
    }

    public static function userinfo(string $access_token, $openid): UserInfo
    {
        return new UserInfo(raw_post(self::$Base . self::$user_info,
            [
                "access_token" => $access_token,
                "openid" => $openid,
            ]
        ));
    }


}

