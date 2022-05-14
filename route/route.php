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

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});


Route::any(':version/:module/:controller/:function', '\app\:version\:module\controller\:controller@:function')->header('Access-Control-Allow-Origin', '*')
    ->header('Access-Control-Allow-Credentials', 'true')
    ->allowCrossDomain();


Route::any('up', '\app\v1\file\controller\index@up')->header('Access-Control-Allow-Origin', '*')
    ->header('Access-Control-Allow-Credentials', 'true')
    ->allowCrossDomain();
Route::any('upfull', '\app\v1\file\controller\index@upfull')
    ->header('Access-Control-Allow-Origin', '*')
    ->header('Access-Control-Allow-Credentials', 'true')
    ->allowCrossDomain();


Route::any(':any', function () {
    return request()::url();
});

Route::any('', function () {
    return request()::url();
});

