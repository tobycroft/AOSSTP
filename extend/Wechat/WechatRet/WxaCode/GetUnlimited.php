<?php

namespace Wechat\WechatRet\WxaCode;


use Wechat\Miniprogram;

class GetUnlimited extends Miniprogram
{
    public $image;
    private $error;

    public function __construct($json)
    {
        $data = json_decode($json, 1);
        if (isset($data["errmsg"])) {
            $this->error = $data["errmsg"];
        } else {
            $this->image = $data;
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

    public function error()
    {
        return $this->error;
    }
}