<?php
namespace app\teacher\validate;

use think\Validate;

class AddClass extends Validate
{
    protected $rule = [
        'class_grade'  =>  'require',
        'class_name' =>  'require',
        'class_staffRoom' =>  'require',
        'class_specialty' =>  'require',
    ];
    protected $message = [
        'class_grade.require'=>'请输入年级',
        'class_name.require'=>'请输入班级名称',
		'class_staffRoom.require'=>'请输入教研室名称',
		'class_specialty.require'=>'请输入专业名称',
	];

}