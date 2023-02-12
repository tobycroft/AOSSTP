<?php

namespace app\v1\wechat\controller;

use app\v1\log\model\LogWebModel;
use app\v1\wechat\model\WechatModel;

class api
{

    public function recv()
    {
        if (request()->isGet()) {
            $this->get();
        } else {
            $this->post();
        }
    }

    public function post()
    {

    }

    public function get()
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

        $project = \Input::Get('project');
        $signature = \Input::Get('signature');
        $timestamp = \Input::Get('timestamp');
        $echostr = \Input::Get('echostr');
        $nonce = \Input::Get('nonce');
        $data = WechatModel::where('project', $project)->find();
        if (!$data) {
            \Ret::Fail(401, $project, '项目不可用');
        }

        $tmpArr = array($data['token'], $timestamp, $nonce);
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