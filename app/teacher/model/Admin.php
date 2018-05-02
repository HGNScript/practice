<?php
namespace app\teacher\model;

use think\Model;

class Admin extends Model {

	protected $pk = "tch_id";
	protected $table = "practice_teacher";

	protected $insert = [ 'password' ];

	/**
	 * 用户默认密码
	 * @param [type] $value [description]
	 */
	protected function setPasswordAttr ($value ) {
		return md5('gzcj');
	}
	/**
	 * 判断工号是否存在
	 */
	public function numBer($numBer) {
		return $this->where('tch_numBer', $numBer)->find();

	}
	/**
	 * 获取用户权限
	 */
	public function getAuthority($tch_id){
		return $this->where('tch_id', $tch_id)->value('authority');
	}

	/**
	 * 教师模糊查询
	 */
	public function check($data){
		return $this->where("authority=2 AND tch_numBer like '%$data%' OR authority=2 AND tch_name like '%$data%'")->select();
	}
	/**
	 * 获取权限为2的用户
	 * @return [type] [description]
	 */
	public function getRes() {
		return $this->where('authority', 2)->order('tch_numBer')->select();
	}
	/**
	 * 添加用户
	 */
	public function addAdmin($data){
		return $this->save($data);
	}

	public function edit($data, $tch_id) {
		return $this->save($data, ['tch_id' => $tch_id]);
	}

}