<?php

namespace app\v1\wechat\controller;

use BaseController\CommonController;

class index extends CommonController
{

    public function initialize()
    {
        parent::initialize();
        $this->token = input('get.token');
        if (!$this->token) {
            \Ret::fail('token');
        }
    }

    public function ()
    {

    }
}