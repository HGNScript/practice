<?php
namespace app\index\model;
use think\Model;
use think\Dd;

class Signin extends Model{
	protected $pk = "sginIn_id";
	protected $table = "practice_sginin";
	protected $insert = [ 'sendtime' ];

	protected function setSendTimeAttr ($value ) {
		return time();
	}

	public function addSignIn($data){
		return $this->save($data);
	}
	public function getSignIn($stu_id){
		return $this->where('stu_id', $stu_id)->order('sendtime desc')->limit(1)->select();
	}
}