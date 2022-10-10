<?php

namespace Wechat;

use Wechat\WechatRet\GetAccessToken;

class Miniprogram extends WechatUrl
{
    protected static $Base = "https://api.weixin.qq.com";

    public static function getAccessToken(string $appid, $secret, $grant_type = "client_credential"): GetAccessToken
    {
        $data = new GetAccessToken(
            raw_post(self::$Base . self::$getAccessToken, [
                "appid" => $appid,
                "secret" => $secret,
                "grant_type" => $grant_type,
            ])
        );
        return $data;
    }

    public static function getWxaCodeUnlimit(string $access_token, $scene, $page, $width, $env_version = "release")
    {
        return raw_post(http_build_url(self::$Base . self::$getUnlimited, ["access_token" => $access_token]), [
                "access_token" => $access_token,
                "scene" => $scene,
                "page" => $page,
                "width" => $width,
                "env_version" => $env_version,
            ]
        );
    }
}


function raw_post($send_url, $postData = [])
{
    $headers = array("Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache");
    $postData = json_encode($postData, 320);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $send_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}