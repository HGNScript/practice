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
use think\Route;
// return [
//     // '__pattern__' => [
//     //     'name' => '\w+',
//     // ],
//     // '[hello]'     => [
//     //     ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//     //     ':name' => ['index/hello', ['method' => 'post']],
//     // ],

// ];

Route::rule('tchLogin','teacher/Login/index');
// Route::rule('Out','teacher/index/out.html');
Route::rule('tch','teacher/Index/index');

Route::rule('stuLogin','index/Login/index');
Route::rule('stu','index/Index/index');
Route::rule('info','index/index/editadd');
Route::rule('logs','index/index/logs');
Route::rule('notice','index/index/notice');
Route::rule('editpas','index/index/editpas');
Route::rule('clogs','index/index/clogs');

