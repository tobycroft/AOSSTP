<?php

namespace BaseController;

use think\Controller;

class CommonController extends Controller
{
    public function initialize()
    {
        header("Access-Control-Allow-Origin: *", true);
        header("Access-Control-Max-Age: 86400", true);
        header("Access-Control-Allow-Credentials: false", true);
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE", true);
        header("Access-Control-Allow-Headers: *", true);
        // 服务启动
    }
}