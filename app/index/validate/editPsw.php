<?php
namespace app\index\validate;

use think\Validate;

class editPsw extends Validate
{
    protected $rule = [
        'stu_name'  =>  'require',
        'stu_numBer' =>  'require',
        'identity' => 'require',
        'oldPsw' => 'require',
        'newPsw' => 'require',
    ];
    protected $message = [
        'stu_name.require'=>'请输入姓名',
		'stu_numBer.require'=>'请输入学号',
        'identity.require'=>'请输入身份证号码',
        'oldPsw.require'=>'请输入密码',
        'newPsw.require'=>'请重新输入密码',
	];

}