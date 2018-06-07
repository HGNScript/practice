<?php
namespace app\index\model;

use think\Model;

class Index extends Model {

	protected $pk = "stu_id";
	protected $table = "practice_student";

	public function getInfo($stu_id) {
		return $this->alias('s')
		->join('practice_company c','s.stu_id = c.stu_id', 'left')
		->join('practice_class l','s.class_id = l.class_id')
		->join('practice_teacher t','l.tch_id = t.tch_id')
		->where('s.stu_id', $stu_id)
		->order('sendtime desc')
		->find();
	}

	public function getCompany($stu_id) {
		return $this->alias('s')
		->join('practice_company c','s.stu_id = c.stu_id')
		->where('s.stu_id', $stu_id)
		->order('sendtime desc')
		->limit(1)
		->find();
	}

	public function editInfo($data, $stu_id) {
		return $this->allowField(true)->save($data, ['stu_id' => $stu_id]);
	}
	public function signInfo($stu_id) {
		return $this->where('stu_id', $stu_id)->where('signInFlag', 1)->find();
	}

	public function writeFlag($stu_id, $flag) {
		$this->save(['logsFlag'  => $flag],['stu_id' => $stu_id]);
	}

	public function signInFlag($stu_id, $flag) {
		$this->save(['signInFlag'  => $flag],['stu_id' => $stu_id]);
	}
	public function getLogsFlag($stu_id) {
		return $this->where('stu_id', $stu_id)->where('logsFlag', 1)->select();
	}

}