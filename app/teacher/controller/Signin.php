<?php
namespace app\teacher\controller;
use think\Controller;

class Signin extends Controller{

	public function index(){
		$stu_id = db('student')->column('stu_id');

		foreach ($stu_id as $key => $value) {
			db('student')->where('stu_id', $value)->update(['signInFlag' => 0]);
		}
	}


}