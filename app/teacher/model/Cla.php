<?php
namespace app\teacher\model;

use think\Model;

class Cla extends Model {

	protected $pk = "class_id";
	protected $table = "practice_class";

	/**
	 * 获取班级数据
	 */
	public function getRes($tch_id){
		return $this->where('tch_id', $tch_id)->order('class_grade desc')->select();
	}
	/**
	 * 获取筛选后的数据
	 */
	public function getCate($data, $flag){
		if (!$flag) {
			if ($data[0] && $data[1] && $data[2]) {
				return $this->where('tch_id', 0)
						->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[1]) {
				return $this->where('tch_id', 0)
						->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[2]) {
				return $this->where('tch_id', 0)
						->where('class_grade', $data[0])
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[1] && $data[2]) {
				return $this->where('tch_id', 0)
						->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[0]) {
				return $this->where('tch_id', 0)->where('class_grade', $data[0])
						->order('class_grade desc')
						->select();
			}
			if ($data[1]) {
				return $this->where('tch_id', 0)->where('class_staffRoom', trim($data[1]))
						->order('class_grade desc')
						->select();
			}
			if ($data[2]) {
				return $this->where('tch_id', 0)->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			return $this->alias('c')->where('tch_id', 0)
				->order('class_grade desc')
				->select();

		} else {
			if ($data[0] && $data[1] && $data[2] && $data[3]) {
				return $this->alias('c')
						->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')
						->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->where('c.tch_id', $data[3])
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[1] && $data[2]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[1]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[2]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_grade', $data[0])
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[3]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_grade', $data[0])
						->where('c.tch_id', $data[3])
						->order('class_grade desc')
						->select();
			}
			if ($data[1] && $data[2]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[1] && $data[3]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_staffRoom', trim($data[1]))
						->where('c.tch_id', $data[3])
						->order('class_grade desc')
						->select();
			}
			if ($data[2] && $data[3]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_specialty', $data[2])
						->where('c.tch_id', $data[3])
						->order('class_grade desc')
						->select();
			}


			if ($data[0]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_grade', $data[0])
						->order('class_grade desc')
						->select();
			}
			if ($data[1]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_staffRoom', trim($data[1]))
						->order('class_grade desc')
						->select();
			}
			if ($data[2]) {
				return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[3]) {
				return $this->alias('c')
					    ->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')
						->where('c.tch_id', $data[3])
						->order('class_grade desc')
						->select();
			}

			return $this->alias('c')
						->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')
						->order('c.tch_id')
						->select();
		}
	}

	/**
	 * 未分配的班级模糊查询
	 * @param  [type] $data 关键字
	 */
	public function searchCla($data){
		return $this->where("tch_id=0 AND class_grade like '%$data%' OR tch_id=0 AND class_name like '%$data%' OR tch_id=0 AND class_staffRoom like '%$data%' OR tch_id=0 AND class_specialty like '%$data%'")->order('class_grade desc')->select();
	}

	/**
	 * 获取指定年级的数据
	 * @return [type] [description]
	 */
	public function getCla() {
		return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')
					->order('c.tch_id')
					->select();
	}
	/**
	 * 班级的模糊查询
	 */
	public function search($data){
		return $this->alias('c')
					->join('practice_teacher t','c.tch_id = t.tch_id', 'LEFT')
					->where("class_grade like '%$data%' OR class_name like '%$data%' OR class_staffRoom like '%$data%' OR class_specialty like '%$data%'")->order('class_grade desc')
					->order('class_grade desc')
					->select();
	}

	/**
	 * 添加班级
	 */
	public function addClass($data) {
		return $this->save($data);
	}

	public function edit($data, $class_id) {
		return $this->save($data, ['class_id' => $class_id]);
	}


	public function exportClass($data){
		if ($data[0] && $data[1] && $data[2]) {
				return $this->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[1] && $data[2]) {
				return $this->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[1]) {
				return $this->where('class_grade', $data[0])
						->where('class_staffRoom', trim($data[1]))
						->order('class_grade desc')
						->select();
			}
			if ($data[0] && $data[2]) {
				return $this->where('class_grade', $data[0])
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}
			if ($data[1] && $data[2]) {
				return $this->where('class_staffRoom', trim($data[1]))
						->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}


			if ($data[0]) {
				return $this->where('class_grade', $data[0])
						->order('class_grade desc')
						->select();
			}
			if ($data[1]) {
				return $this->where('class_staffRoom', trim($data[1]))
						->order('class_grade desc')
						->select();
			}
			if ($data[2]) {
				return $this->where('class_specialty', $data[2])
						->order('class_grade desc')
						->select();
			}

			return $this->order('class_grade desc')->select();
	}

}