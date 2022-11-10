<?php

namespace app\v1\logger\controller;

use app\v1\logger\model\LoggerCrrModel;
use app\v1\project\model\ProjectModel;
use BaseController\CommonController;

class crr extends CommonController
{

    public $token;
    public $proc;

    public function initialize()
    {
        $this->token = input("token");
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->proc = ProjectModel::api_find_token($this->token);
        if (!$this->proc) {
            \Ret::Fail(403, "项目不可用");
        }
    }

    public function index()
    {
        LoggerCrrModel::create();
    }
}