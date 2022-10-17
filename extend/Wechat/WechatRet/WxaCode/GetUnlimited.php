<?php

namespace Wechat\WechatRet\WxaCode;

class GetUnlimited
{
    public $response;
    public $image;
    private $error;

    public function __construct($json)
    {
        try {
            $this->response = $json;
            $data = json_decode($json, 1);
            if (isset($data['errmsg'])) {
                $this->error = $data['errmsg'];
            } else {
                $this->image = $json;
            }
        } catch (\Exception $e) {
            $this->response = $json;
            $this->error = $e->getMessage();
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

    public function getError()
    {
        return $this->error;
    }

}