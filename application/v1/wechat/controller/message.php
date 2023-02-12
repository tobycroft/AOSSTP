<?php

namespace app\v1\wechat\controller;

use Input;
use Ret;
use Wechat\KefuMessage;

class message extends offiaccount
{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function send()
    {
        $openid = Input::Post('openid');
        $content = Input::Post('content');
        $kefu = new KefuMessage($this->access_token, $openid);
        $kefu->text($content);
        $ret = $kefu->send();
        if ($ret->isSuccess()) {
            Ret::Success();
        } else {
            Ret::Fail(200, $ret->response, $ret->getError());
        }
    }
}