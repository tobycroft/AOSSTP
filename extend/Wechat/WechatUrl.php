<?php

namespace Wechat;

class WechatUrl
{
    protected static string $getAccessToken = "/cgi-bin/token";
    protected static string $getUnlimited = "/wxa/getwxacodeunlimit";
    protected static string $jscode2session = "/sns/jscode2session";
}