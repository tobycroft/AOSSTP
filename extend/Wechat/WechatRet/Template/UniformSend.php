<?php

namespace Wechat\WechatRet\Template;


class UniformSend
{

    public $response;
    protected $data;
    private $error;

    public function __construct($json)
    {
        $this->response = $json;
        $data = json_decode($json, 1);
        if (isset($data['errmsg'])) {
            $this->error = $data['errmsg'];
        } else {
            $this->data = $json;
        }
    }

    public function isSuccess()
    {
        if (isset($this->error)) {
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