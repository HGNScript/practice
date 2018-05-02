<?php
namespace app\common\validate;

use think\Validate;

class EidtAdmin extends Validate
{
    protected $rule = [
        'admin_username'  => 'require',
        'oldpassword'  => 'require',
    ];
    protected $message = [
		'admin_username.require'=>'请输入管理员名称',
		'oldpassword.require'=>'请输入密码',
	];

}