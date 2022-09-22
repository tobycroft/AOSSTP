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

\think\facade\Route::any(':version/:module/:controller/:function', '\app\:version\:module\controller\:controller@:function');


\think\facade\Route::any('up', '\app\v1\file\controller\index@up');

\think\facade\Route::any('upfull', '\app\v1\file\controller\index@upfull');


\think\facade\Route::any(':any', function () {
    return \think\facade\Request::url();
});

\think\facade\Route::any('', function () {
    return 'AOSS is a public oss platform!';
});

