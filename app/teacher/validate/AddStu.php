<?php
namespace app\teacher\validate;

use think\Validate;

class AddStu extends Validate
{
    protected $rule = [
        'stu_numBer'  =>  'require',
        'stu_name'  =>  'require',
        'identity' =>  'require',
        'stu_phone' =>  'require',
        'classteacher' =>  'require',
        'classteacher_phone' =>  'require',
    ];
    protected $message = [
        'stu_numBer.require'=>'请输入学号',
        'stu_name.require'=>'请输入名称',
        'identity.require'=>'请输入身份证',
        'stu_phone.require'=>'请输入联系电话',
        'classteacher.require'=>'请输入班主任名称',
		'classteacher_phone.require'=>'请输入班主任联系电话',
	];

}