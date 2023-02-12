<?php

namespace app\v1\wechat\controller;

use app\v1\file\controller\search;
use app\v1\log\model\LogWebModel;
use app\v1\wechat\model\WechatModel;
use WechatSig\XMLParse;

class api extends search
{


    public function initialize()
    {
        //微信验证
        $in = \Input::Raw();
        LogWebModel::create([
            'get' => json_encode(request()->get()),
            'post' => json_encode(request()->post()),
            'raw' => $in,
            'header' => json_encode(request()->header()),
            'method' => request()->method(),
        ]);
    }

    public function recv()
    {
        $project = \Input::Get('project');
        $data = WechatModel::where('project', $project)->find();
        if (!$data) {
            \Ret::Fail(401, $project, '项目不可用');
        }
        $this->token = $data["token"];
        $this->proc = $data;
        if (request()->isGet()) {
            $this->get();
        } else {
            $this->post();
        }
    }

    public function post()
    {

        $xmltext = \Input::Raw();
        $parse = new XMLParse();
        $msg = $parse->extract($xmltext);
        LogWebModel::create([
            'get' => json_encode(request()->get()),
            'post' => json_encode(request()->post()),
            'raw' => json_encode($msg, 320),
            'header' => json_encode(request()->header()),
            'method' => request()->method(),
        ]);
    }

    public function get()
    {


        $signature = \Input::Get('signature');
        $timestamp = \Input::Get('timestamp');
        $echostr = \Input::Get('echostr');
        $nonce = \Input::Get('nonce');


        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $echostr;
        } else {
            echo 0;
        }
    }

}