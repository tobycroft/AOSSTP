<?php

namespace Wechat;

class WechatUrl
{
    protected static string $getAccessToken = "/cgi-bin/token";
    protected static string $getUnlimited = "/wxa/getwxacodeunlimit";
    protected static string $jscode2session = "/sns/jscode2session";
    protected static string $getuserphonenumber = "/wxa/business/getuserphonenumber";
    protected static string $generatescheme = "/wxa/generatescheme";
}