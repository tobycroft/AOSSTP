<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


\think\facade\Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

\think\facade\Route::any(':version/:module/:controller/:function', function () {
    if (\think\facade\Request::isOptions()) {
        header("Access-Control-Allow-Origin: *", true);
        header("Access-Control-Max-Age: 86400", true);
        header("Access-Control-Allow-Credentials: true", true);
        header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS", true);
        header("Access-Control-Allow-Headers: *", true);
        return;
    }
    return \think\facade\Route::make('\app\:version\:module\controller\:controller@:function');
});


\think\facade\Route::any('up', '\app\v1\file\controller\index@up');

\think\facade\Route::any('upfull', '\app\v1\file\controller\index@upfull');


\think\facade\Route::any(':any', function () {
    header("Access-Control-Allow-Origin: *", true);
    header("Access-Control-Max-Age: 86400", true);
    header("Access-Control-Allow-Credentials: true", true);
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS", true);
    header("Access-Control-Allow-Headers: *", true);
    return \think\facade\Request::url();
});

\think\facade\Route::any('', function () {
    header("Access-Control-Allow-Origin: *", true);
    header("Access-Control-Max-Age: 86400", true);
    header("Access-Control-Allow-Credentials: true", true);
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS", true);
    header("Access-Control-Allow-Headers: *", true);
    return \think\facade\Request::url();
});

