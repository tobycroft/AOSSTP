<?php

namespace Wechat;

use Wechat\WechatRet\GetAccessToken;

class Miniprogram extends WechatUrl
{
    protected static $Base = "https://api.weixin.qq.com";

    public static function getAccessToken(string $appid, $secret, $grant_type = "client_credential"): GetAccessToken
    {

        return new GetAccessToken(
            raw_post(self::$Base . self::$getAccessToken . http_build_query([
                        "appid" => $appid,
                        "secret" => $secret,
                        "grant_type" => $grant_type,
                    ]
                )
            )
        );
    }


}

function raw_post($send_url, $postData = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $send_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}