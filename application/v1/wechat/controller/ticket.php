<?php

namespace app\v1\wechat\controller;

use app\v1\wechat\model\WechatModel;

class ticket extends wxa
{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $ticket_expire_after = strtotime($this->wechat['ticket_expire_after']);
        if ($ticket_expire_after < time() || empty($this->access_token)) {
            $this->getticket();
        }
    }

    public function getticket()
    {

        $ticket = \Wechat\Ticket::getTicket($this->access_token);
        if ($ticket->isSuccess()) {
            WechatModel::where("project", $this->token)->update([
                "ticket" => $ticket->ticket,
                'ticket_expire_after' => date('Y-m-d H:i:s', $ticket->expires_in + time() - 600)
            ]);
            $this->wechat = WechatModel::where("project", $this->token)->find();
        } else {
            \Ret::Fail(300, $ticket->response, $ticket->error());
        }
    }

    public function signature()
    {
        $noncestr = input("noncestr") ?: \Ret::Fail(400, null, 'noncestr');
        $timestamp = input('timestamp') ?: \Ret::Fail(400, null, 'timestamp');
        $url = input('url') ?: \Ret::Fail(400, null, 'url');
        $post = [
            'noncestr' => $noncestr,
            'jsapi_ticket' => this->wechat['ticket'],
            'timestamp' => $timestamp,
            'url' => $url,
        ];
        ksort($post);
        $str = http_build_query($post, "", null, 0);
        \Ret::Success(0, sha1(urldecode($str))，);
    }

}