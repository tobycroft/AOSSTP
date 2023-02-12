<?php

namespace Wechat\WechatRet\Template;


class UniformSend
{

    public $response;
    protected $data;
    private $error;
    protected int $errcode = 0;

    public function __construct($json)
    {
        $this->response = $json;
        $data = json_decode($json, 1);
        if (isset($data['errmsg']) && isset($data['errcode'])) {
            $this->error = $data['errmsg'];
            $this->errcode = $data['errcode'];
        } else {
            $this->data = $json;
        }
    }

    public function isSuccess()
    {
        if (isset($this->error) && $this->errcode !== 0) {
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