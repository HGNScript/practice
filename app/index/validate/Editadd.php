<?php
namespace app\index\validate;

use think\Validate;

class editadd extends Validate
{
    protected $rule = [
        'company_name'  =>  'require',
        'company_address' =>  'require',
        'company_salary' => 'require',
        'company_position' => 'require',
        'principal' => 'require',
        'principal_phone' => 'require',
        'stu_phone' => 'require',
        'date' => 'require',
    ];
    protected $message = [
        'company_name.require'=>'请输入实习单位名称',
		'company_address.require'=>'请输入实习单位地址',
        'company_salary.require'=>'请输入月薪',
        'company_position.require'=>'请输入实习职位',
        'principal.require'=>'请输入实习负责人名称',
        'principal_phone.require'=>'请输入实习负责人联系电话',
        'stu_phone.require'=>'请输入学生电话',
        'date.require'=>'请输入学生电话',
	];

}