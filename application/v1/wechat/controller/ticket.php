<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\action\AccessTokenAction;
use think\Request;

class ticket extends wxa
{
    public function getticket(Request $request)
    {

        $ticket = \Wechat\Ticket::getTicket($this->access_token);
        if ($ticket->isSuccess()) {
            \Ret::Success(0, $ticket->getData());
        } else {
            $this->ac->auto_error_code($ticket->getErrcode());
            \Ret::Fail(300, $ticket->response, $ticket->getError());
        }
    }

    public function signature(Request $request)
    {
        $expire_after = strtotime($wechat['expire_after']);
        $this->ac = new AccessTokenAction($this->token, $this->appid, $this->appsecret);
        if ($expire_after < time() || empty($this->access_token)) {
            $this->ac->refresh_token();
            $this->access_token = $this->ac->get_access_token();
        }
    }

}