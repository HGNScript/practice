<?php
namespace app\index\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'stu_numBer'  =>  'require',
        'stu_password' =>  'require',
        'verification'=>'require|captcha'
    ];
    protected $message = [
		'stu_numBer.require'=>'请输入学号',
		'stu_password.require'=>'请输入密码',
        'verification.require'=> '请输入验证码',
		'verification.captcha'=>'验证码不正确',
	];

}