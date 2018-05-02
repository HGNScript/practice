<?php
namespace app\teacher\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'tch_numBer'  =>  'require',
        'password' =>  'require',
        'verification'=>'require|captcha'
    ];
    protected $message = [
		'tch_numBer.require'=>'请输入工号',
		'password.require'=>'请输入密码',
        'verification.require'=> '请输入验证码',
		'verification.captcha'=>'验证码不正确',
	];

}