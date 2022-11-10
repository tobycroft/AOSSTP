<?php

namespace Wechat\WechatRet;


class UserGet
{
    public $response;

    protected $data;
    protected mixed $error;

    public mixed $total;
    public mixed $count;
    public array $openid;

    public function __construct($json)
    {
        $this->response = $json;
        $data = json_decode($json, 1);
        if (isset($data['errmsg'])) {
            $this->error = $data['errmsg'];
        } else {
            $this->data = $data['data'];
            $this->total = $data['total'] ?? "";
            $this->count = $data['count'] ?? "";
            $this->openid = $data["data"]['openid'] ?? [];
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