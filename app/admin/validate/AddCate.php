<?php
namespace app\common\validate;

use think\Validate;

class AddCate extends Validate
{
    protected $rule = [
        'cate_name'  =>  'require',
        'cate_pid' =>  'require',
        'cate_sort' =>  'require',
    ];
    protected $message = [
		'cate_name.require'=>'请输入分类名',
		'cate_pid.require'=>'选择所属分类',
		'cate_sort.require'=>'请输入序号',
	];

}