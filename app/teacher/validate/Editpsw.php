<?php
namespace app\teacher\validate;

use think\Validate;

class Editpsw extends Validate
{
    protected $rule = [
        'oldtch_psw'  =>  'require',
        'newtch_psw' =>  'require|alphaDash',
        'newtch_psws' =>  'require|alphaDash',
    ];
    protected $message = [
        'oldtch_psw.require'=>'请输入旧密码',
        'newtch_psw.require'=>'请输入新密码',
        'newtch_psw.alphaDash'=>'密码只能为数字和英文',
        'newtch_psws.require'=>'请输入新密码',
        'newtch_psws.alphaDash'=>'密码只能为数字和英文',
	];

}