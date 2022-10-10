<?php

namespace app\v1\image\controller;

use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use chillerlan\QRCode\QRCode;
use think\Request;

class qr extends CommonController
{


    public mixed $token;
    public mixed $proc;

    public function initialize()
    {
        set_time_limit(0);
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            \Ret::fail('项目不可用');
        }
    }

    public function png(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        $json = input("data");
        $qr = new QRCode();
        echo $qr->render($json);
        \think\facade\Response::contentType("image/png")->send();
    }

    public function base64(Request $request)
    {
        if (!$request->has("data")) {
            \Ret::fail("data");
        }
        $json = input("data");
        $qr = new QRCode();
        echo $qr->render($json);
    }


}