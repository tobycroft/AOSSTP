<?php

namespace app\v1\wechat\controller;

use think\Request;
use Wechat\OfficialAccount;

class ticket extends wxa
{
    public function getticket(Request $request)
    {

        $wxa = OfficialAccount::userinfo($this->access_token, $openid);
        if ($wxa->isSuccess()) {
            \Ret::Success(0, $wxa->getData());
        } else {
            $this->ac->auto_error_code($wxa->getErrcode());
            \Ret::Fail(300, $wxa->response, $wxa->getError());
        }
    }

}