<?php
namespace app\teacher\validate;

use think\Validate;

class EditLogin extends Validate
{
    protected $rule = [
        'tch_numBer'  =>  'require',
        'tch_phone' =>  'require',
        'newtch_psw' =>  'require|alphaDash',
        'newtch_psws' =>  'require|alphaDash',
    ];
    protected $message = [
        'tch_numBer.require'=>'请输入工号',
        'tch_phone.require'=>'请输入电话号码',
        'newtch_psw.require'=>'请输入新密码',
        'newtch_psw.alphaDash'=>'密码只能为数字和英文',
        'newtch_psws.require'=>'请再次输入新密码',
        'newtch_psws.alphaDash'=>'密码只能为数字和英文',
	];

}