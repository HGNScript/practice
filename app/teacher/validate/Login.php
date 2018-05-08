<?php
namespace app\teacher\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'tch_numBer'  =>  'require',
        'password' =>  'require',
    ];
    protected $message = [
		'tch_numBer.require'=>'请输入工号',
		'password.require'=>'请输入密码',
	];

}