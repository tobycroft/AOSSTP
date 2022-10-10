<?php

namespace Wechat;

use Wechat\WechatRet\GetAccessToken;

class Miniprogram extends WechatUrl
{
    protected static $Base = "https://api.weixin.qq.com";

    public static function getAccessToken(string $appid, $secret, $grant_type = "client_credential"): GetAccessToken
    {
        $addr = self::$Base . self::$getUnlimited . "?" . http_build_query([
                "appid" => $appid,
                "secret" => $secret,
                "grant_type" => $grant_type,
            ]);

        $data = new GetAccessToken(
            raw_post($addr)
        );
        return $data;
    }

    public static function getWxaCodeUnlimit(string $access_token, $scene, $page, $width, $env_version = "release")
    {
        $addr = self::$Base . self::$getUnlimited . "?" . http_build_query(["access_token" => $access_token]);
//        echo $addr;
        return raw_post($addr, [
//                "access_token" => $access_token,
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
    echo $postData;
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