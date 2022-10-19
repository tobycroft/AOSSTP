<?php

namespace app\v1\file\controller;

use app\v1\project\model\ProjectModel;
use think\Request;

class sig
{
    public $token;
    public $proc;

    public function initialize()
    {
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::Fail(401, null, 'token');
        }
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            \Ret::Fail(401, null, '项目不可用');
        }
    }

    public function get(Request $request)
    {
        
    }

}