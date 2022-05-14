<?php

namespace BaseController;

use think\Controller;

class CommonController extends Controller
{
    public function initialize()
    {
        header("Access-Control-Allow-Origin: *", true);
//        header("access-control-allow-origin: *", true);
        header("Access-Control-Allow-Credentials: true", true);
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE", true);
        header("Access-Control-Allow-Headers: *", true);
        // 服务启动
    }
}