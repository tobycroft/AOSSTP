<?php

namespace app\v1\rsa\controller;


use app\v1\rsa\action\Rsa;
use BaseController\CommonController;

class index extends CommonController
{

    public function index()
    {
        $rsa = new Rsa();
        $sign = $rsa->createSign("test");
        \Ret::Success(0, $sign);

    }
}