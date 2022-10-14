<?php

namespace Wechat\WechatRet\WxaCode;

class GenerateScheme
{
    public $response;
    public mixed $openlink;
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
            $this->openlink = $this->data["openlink"];
        }
        return $this;
    }

    public function isSuccess(): bool
    {
        if ($this->error) {
            return false;
        } else {
            return true;
        }
    }

    public function getError(): mixed
    {
        return $this->error;
    }

}