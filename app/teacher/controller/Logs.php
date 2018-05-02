<?php
namespace app\teacher\controller;
use think\Controller;

class Logs extends Controller{

	public function index(){
		$stu_id = db('student')->column('stu_id');

		foreach ($stu_id as $key => $value) {
			db('student')->where('stu_id', $value)->update(['logsFlag' => 0]);
		}
	}


}