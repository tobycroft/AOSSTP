<?php

namespace Wechat;

class WechatUrl
{
    protected static string $getAccessToken = "/cgi-bin/token";
    protected static string $getUnlimited = "/wxa/getwxacodeunlimit";
    protected static string $jscode2session = "/sns/jscode2session";
    protected static string $getuserphonenumber = "/wxa/business/getuserphonenumber";
    protected static string $generatescheme = "/wxa/generatescheme";


    protected static string $user_get = "/cgi-bin/user/get";
    protected static string $user_info = "/cgi-bin/user/info";

    protected static string $uniform_send = "/cgi-bin/message/wxopen/template/uniform_send";


    public static function getAccessTokenPath()
    {
        return self::$getAccessToken;
    }

}