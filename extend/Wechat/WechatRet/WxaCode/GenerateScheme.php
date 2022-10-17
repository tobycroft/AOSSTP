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
        try {
            $this->response = $json;
            $data = json_decode($json, 1);
            if (isset($data['errmsg'])) {
                $this->error = $data['errmsg'];
            } else {
                $this->data = $json;
                $this->openlink = $this->data['openlink'];
            }
        } catch (\Exception $e) {
            $this->response = $json;
            $this->error = $e->getMessage();
        }

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