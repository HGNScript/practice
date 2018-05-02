<?php
namespace app\teacher\validate;

use think\Validate;

class Add extends Validate
{
    protected $rule = [
        'tch_numBer'  =>  'require|length:4,10',
        'tch_name' =>  'require',
        'tch_phone' =>  'require|length:9,12',
        'tch_email' =>  'require|email',
    ];
    protected $message = [
        'tch_numBer.require'=>'请输入教师工号',
        'tch_numBer.length'=>'工号必须在4~10个字符之间',
        'tch_phone.require'=>'请填写联系电话',
        'tch_phone.length'=>'联系电话必须在9~12个字符之间',
		'tch_name.require'=>'请输入教师名称',
		'tch_email.require'=>'请输入邮箱地址',
		'tch_email.email'=>'邮箱格式错误',
	];

}