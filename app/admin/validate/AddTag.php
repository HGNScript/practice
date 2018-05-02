<?php
namespace app\common\validate;

use think\Validate;

class AddTag extends Validate
{
    protected $rule = [
        'tag_name'  =>  'require',
        'tag_sort' =>  'require',
    ];
    protected $message = [
		'tag_name.require'=>'请输入标签名',
		'tag_sort.require'=>'请输入序号',
	];

}