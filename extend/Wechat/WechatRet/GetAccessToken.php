<?php

namespace Wechat\WechatRet;


use Wechat\Miniprogram;

class GetAccessToken extends Miniprogram
{
    public $access_token;
    public $expires_in;
    private $error;

    public function __construct($json)
    {
        $data = json_decode($json, 1);
        if (isset($data["errmsg"])) {
            $this->error = $data["errmsg"];
        } else {
            $this->access_token = $data["access_token"];
            $this->expires_in = $data["expires_in"];
        }
    }

    public function isSuccess()
    {
        if ($this->error) {
            return false;
        } else {
            return true;
        }
    }
}