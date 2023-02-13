<?php

namespace Wechat\WechatRet\Offi;

class GetUnlimited
{
    public $response;
    public $ticket;
    public $expire_seconds;
    public $url;
    protected int $errcode = 0;
    private $error;

    public function __construct($json)
    {
        $this->response = $json;
        $data = json_decode($json, 1);
        if (isset($data['ticket']) && isset($data['expire_seconds']) && isset($data['url'])) {
            $this->ticket = $data["ticket"];
            $this->expire_seconds = $data["expire_seconds"];
            $this->url = $data["url"];
        } else {
            $this->error = $data['errmsg'];
            $this->errcode = $data['errcode'];
        }
    }

    public function isSuccess()
    {
        if (isset($this->error) && $this->errcode != "0") {
            return false;
        } else {
            return true;
        }
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrcode(): int
    {
        return $this->errcode;
    }
}