<?php

namespace app\v1\sms\controller;

use app\v1\project\model\ProjectModel;
use BaseController\CommonController;
use Input;
use Ret;
use think\Request;

class single extends CommonController
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
        $ts = Input::PostInt('ts');
        $sign = Input::Post('sign');
        if (md5($this->token . $ts) != $sign) {
            Ret::Fail(401, null, '签名不正确，加密方式为小写MD5(token+ts)');
        }
    }

    public function push(Request $request)
    {
        $phone = Input::Post("phone");
        $quhao = Input::Post("quhao");
        $text = Input::Post("text");
        SendAction::AutoSend($this->proc, $phone, $text);
    }
}