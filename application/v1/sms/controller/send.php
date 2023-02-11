<?php

namespace app\v1\sms\controller;

use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use Input;
use Ret;
use think\Request;

class send extends CommonController
{

    public mixed $token;
    public mixed $proc;

    public function initialize()
    {
        $this->token = Input::Post('name');
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            Ret::Fail(401, null, '项目不可用');
        }
        $ts = Input::Post('ts');
        $sign = Input::Post('ts');
        $name = Input::Post('name');

    }

    public function send(Request $request)
    {
        $phone = Input::Post("phone");
//        SendAction::AutoSend($this->proc, $phone, $param);
    }
}