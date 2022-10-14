<?php

namespace Wechat\WechatRet\WxaCode;

class Jscode2Session
{
    public $response;
    public mixed $session_key;
    public mixed $unionid;
    public mixed $openid;
    protected $data;
    protected mixed $error;

    public function __construct($json)
    {
        $this->response = $json;
        $data = json_decode($json, 1);
        if (($data['errcode'] !== 0)) {
            $this->error = $data['errmsg'];
        } else {
            $this->data = $json;
            $this->openid = $this->data["openid"];
            $this->session_key = $this->data["session_key"];
            $this->unionid = $this->data["unionid"];
        }
        return $this;
    }

    public function isSuccess()
    {
        if ($this->error) {
            return false;
        } else {
            return true;
        }
    }

    public function getError()
    {
        return $this->error;
    }

}