<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Response;

function json($data = [], $code = 200, $header = [], $options = [])
{
    header("Access-Control-Allow-Origin: *", true);
    header("Access-Control-Max-Age: 86400", true);
    header("Access-Control-Allow-Credentials: true", true);
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE", true);
    header("Access-Control-Allow-Headers: *", true);
    return Response::create($data, 'json', $code, [
        "Access-Control-Allow-Origin" => "*",
        "Access-Control-Max-Age" => "86400",
        "Access-Control-Allow-Credentials" => "true",
        "Access-Control-Allow-Methods" => "*",
        "Access-Control-Allow-Headers" => "*",
    ], $options);
}