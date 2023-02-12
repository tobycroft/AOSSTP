<?php

namespace app\v1\wechat\controller;

use app\v1\file\controller\search;
use app\v1\log\model\LogWebModel;
use app\v1\wechat\model\WechatModel;
use Input;
use Ret;

class api extends search
{


    protected $signature;

    protected $timestamp;
    protected $nonce;

    public function initialize()
    {
        $project = Input::Get('project');
        $data = WechatModel::where('project', $project)->find();
        if (!$data) {
            Ret::Fail(401, $project, '项目不可用');
        }
        $this->token = $data['token'];
        $this->proc = $data;

        //微信验证
        $in = Input::Raw();
        LogWebModel::create([
            'get' => json_encode(request()->get()),
            'post' => json_encode(request()->post()),
            'raw' => $in,
            'header' => json_encode(request()->header()),
            'method' => request()->method(),
        ]);

        $this->signature = Input::Get('signature');
        $this->timestamp = Input::Get('timestamp');
        $this->nonce = Input::Get('nonce');

        $tmpArr = array($this->token, $this->timestamp, $this->nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr != $this->signature) {
            Ret::Fail(403);
        }
    }

    public function recv()
    {
        if (request()->isGet()) {
            $this->verify();
        } else {
            $this->message();
        }
    }

    public function message()
    {

        $xmltext = Input::Raw();
        $data = simplexml_load_string($xmltext, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        echo json_encode($data);
        $aaa = $data->getNamespaces("ToUserName");
        echo $aaa;
//        switch ($data['MsgType']) {
//            default:
//                WechatMessageModel::create([
//                    'project' => $this->proc["project"],
//                    'ToUserName' => $data["ToUserName"],
//                ]);
//                break;
//        }

    }


    public function verify()
    {

        echo Input::Get('echostr');


    }

}