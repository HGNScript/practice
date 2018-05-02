<?php
namespace app\teacher\model;

use think\Model;

class Stu extends Model {

	protected $pk = "stu_id";
	protected $table = "practice_student";

	/**
	 * 获取学生数据
	 */
	public function getStu($class_name, $stu_id) {
		if ($class_name) {
			return $this->where('stu_className', $class_name)
				->order('signinFlag, logsFlag')
				->select();
		}
		if ($stu_id) {
			return $this->alias('s')
				->join('practice_company c',' s.stu_id = c.stu_id', 'LEFT')
				->join('practice_class l',' s.stu_className = l.class_name', 'LEFT')
				->join('practice_teacher t',' l.tch_id = t.tch_id', 'LEFT')
				->where('s.stu_id', $stu_id)
				->order('c.sendtime desc')
				->limit(1)
				->select();
		}
	}

	/**
	 * 指定班级学生模糊搜索
	 */
	public function sreach($data, $class_name){
		return $this->where("stu_className= '$class_name' AND stu_numBer like '%$data%' OR stu_className='$class_name' AND stu_name like '%$data%'")->select();
	}

	public function addStu($data){
		return $this->save($data);
	}
	public function edit($data, $stu_id) {
		return $this->save($data, ['stu_id' => $stu_id]);
	}

	public function getSignin($stu_id){
		return $this->alias('s')
				->join('practice_sginin i', 's.stu_id = i.stu_id', 'LEFT')
				->where('s.stu_id', $stu_id)
				->order('i.sendtime')
				->limit(1)
				->select();
	}

	public function getLogs($class_name, $changge){
		if ($changge) {
			return $this->alias('s')
				->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
				->where('s.stu_className', $class_name)
				->order('l.replyFlag, sendtime desc')
				->select();
		} else {
			return $this->alias('s')
				->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
				->where('s.stu_className', $class_name)
				->order('l.replyFlag desc, sendtime desc')
				->select();
		}
	}

	public function getunLogs($stu_id){
		return $this->alias('s')
				->join('practice_logs l', 's.stu_id = l.stu_id', 'left')
				->where('s.stu_id', $stu_id)
				->order('l.sendtime desc')
				->limit(1)
				->select();
	}

	public function search($class_name, $search, $changge){
		if ($changge) {
			return $this->alias('s')
			->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
			->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
			->order('l.replyFlag')
			->select();
		} else {
			return $this->alias('s')
			->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
			->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
			->order('l.replyFlag desc')
			->select();
		}
	}

	public function searchLogs($class_name, $search){
		return $this->where("logsFlag= 0 AND stu_className= '$class_name' AND stu_numBer like '%$search%' OR logsFlag= 0 AND stu_className='$class_name' AND stu_name like '%$search%'")
			->column('stu_id');
	}
	public function searchsign($class_name, $search, $changge){
		if ($changge) {
			return $this->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
			->order('signinFlag')
			->column('stu_id');
		} else {
			return $this->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
			->order('signinFlag desc')
			->column('stu_id');
		}
	}

	public function expot($class_name) {
		return $this->alias('s')
					->join('practice_company c', 's.stu_id = c.stu_id', 'left')
					->join('practice_class l', 's.stu_className = l.class_name', 'left')
					->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
					->where('s.stu_className', $class_name)
					->order('s.stu_numBer')
					->select();
	}

	public function company($stu_id){
		return $this->alias('s')
				->join('practice_company c',' s.stu_id = c.stu_id', 'LEFT')
				->where('s.stu_id', $stu_id)
				->order('c.sendtime desc')
				->select();
	}

}