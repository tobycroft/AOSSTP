<?php

namespace Wechat;

use Wechat\WechatRet\GetAccessToken;
use Wechat\WechatRet\UserGet;

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


}


function raw_post(string $base_url, array $query = [], array $postData = [])
{
    $send_url = $base_url;
    if (!empty($query)) {
        $send_url .= "?" . http_build_query($query);
    }
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