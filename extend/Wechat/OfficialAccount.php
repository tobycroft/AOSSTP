<?php

namespace Wechat;

use Wechat\WechatRet\GetAccessToken;
use Wechat\WechatRet\Template\UniformSend;
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

    public static function uniform_send(string $access_token, $touser, $template_id, $url, $data): UniformSend
    {
        return new UniformSend(raw_post(self::$Base . self::$uniform_send,
            [
                "access_token" => $access_token,
            ],
            [
                "touser" => $touser,
                "mp_template_msg" => [
//                    'appid' => $appid,
                    'template_id' => $template_id,
                    'url' => $url,
                    'data' => $data,
                ],
            ]
        ));
    }

    /*
     *
{
    'touser': 'OPENID',
    'template_id': 'ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY',
    'url': 'http://weixin.qq.com/download',
    'topcolor': '#FF0000',
    'data': {
        'User': {
            'value': '黄先生',
            'color': '#173177'
        },
        'Date': {
            'value': '06月07日 19时24分',
            'color': '#173177'
        },
        'CardNumber': {
            'value': '0426',
            'color': '#173177'
        },
        'Type': {
            'value': '消费',
            'color': '#173177'
        },
        'Money': {
            'value': '人民币260.00元',
            'color': '#173177'
        },
        'DeadTime': {
            'value': '06月07日19时24分',
            'color': '#173177'
        },
        'Left': {
            'value': '6504.09',
            'color': '#173177'
        }
    }
}
     */

    public static function message_template(string $access_token, $openid, $template_id)
    {

    }


}

