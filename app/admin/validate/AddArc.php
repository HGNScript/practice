<?php
namespace app\common\validate;

use think\Validate;

class AddArc extends Validate
{
    protected $rule = [
        'arc_title'  	=>  'require',
        'arc_abstract'	=>  'require',
        'arc_author'  	=>  'require',
        'cate_id'  		=>  'require',
        'arc_sort'  	=>  'require',
        'arc_content'  	=>  'require',
    ];
    protected $message = [
		'arc_title.require'=>'请填写文章标题',
		'arc_abstract.require'=>'请填写文章摘要',
		'arc_author.require'=>'请填写文章作者',
		'cate_id.require'=>'请填写文章分类',
		'arc_sort.require'=>'请请填写文章排序',
		'arc_content.require'=>'请填写文章内容',
	];

}