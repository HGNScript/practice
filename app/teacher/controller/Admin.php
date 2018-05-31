<?php
namespace app\teacher\controller;

class Admin extends Common {
	protected $tch;
	protected $Class;
	protected function _initialize() {
		parent::_initialize();
		$this->tch = new \app\teacher\model\Admin();
		$this->Class = new \app\teacher\model\Cla();
	}
	/**
	 * 教师列表
	 */
	public function admin(){
		$tchData = $this->tch->getRes();
		if (request()->isAjax()) {
			return sizeof($tchData);
		}
		return $this->fetch();
	}
	/**
	 * 教师列表分页
	 * curr 当前页数
	 * limit 每页数据数量
	 */
	public function adminPage(){
		$curr = input('post.curr');
		$limit = input('post.limit');
		$tchData = $this->tch->getRes();
		$data = array();
		foreach ($tchData as $k => $v) {
			$data[$k]['tch_id'] = $v['tch_id'];
			$data[$k]['tch_numBer'] = $v['tch_numBer'];
			$data[$k]['tch_name'] = $v['tch_name'];
			$data[$k]['tch_phone'] = $v['tch_phone'];
			$data[$k]['tch_email'] = $v['tch_email'];
		}
		$star = ($curr-1)*$limit;
		$data = array_slice($data,$star,$limit);
		return json($data);
	}

	/**
	 * 添加教师
	 */
	public function addAdmin(){
		if (request()->isAjax()) {
			$data = input('post.');
			$validate = validate('Add');
			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
				$n = $this->tch->numBer($data['tch_numBer']);
				if ($n) {
					$res = ['valid' => 0, 'msg' => '工号不能重复!'];
				} else{
					$info = $this->tch->addAdmin($data);
				    if ($info) {
				    	$res = ['valid' => 1, 'msg' => '添加成功!'];
				    } else {
				    	$res = ['valid' => 0, 'msg' => '添加失败!'];
				    }
				}
			}
			if($res['valid'])
			{
				return json($res);
			}else{
				return json($res);
			}
		}
		return $this->fetch();
	}

	/**
	 * 导入数据过滤, 过滤工号重复的数据
	 * @param  [type] $data excel表格数据
	 */
	public function arrOnly ($data){
		$datao = [];
		foreach ($data as $key => $value) {
			$datao[$key] = $value['tch_numBer'];
		}
		foreach ($datao as $key => $v) {
			$arr = $datao;
			unset($arr[$key]);
			if (in_array($datao[$key], $arr)) {
		        unset($datao[$key]);
		    }
		}
		$claInfo = [];
		foreach ($data as $key => $value) {
			foreach ($datao as $k => $va) {
				if ($key == $k) {
					array_push($claInfo, $value);
				}
			}
		}
		return $claInfo;
	}

	/**
	 * 接收提交的excl表格文件,获取excel 表格数据,将数据转化为数组
	 * (提交的教师数据,工号不能重复)
	 */
	public function excel() {
		 //import('phpexcel.PHPExcel', EXTEND_PATH);//方法二
	       vendor("PHPExcel.PHPExcel");
	        $objPHPExcel = new \PHPExcel();
	        $file = request()->file('excel');
	        $info = $file->validate(['size'=>15678,'ext'=>'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');
	        if($info){
	            $exclePath = $info->getSaveName();  //获取文件名
	            $file_name = ROOT_PATH .'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址


	            $objReader =new \PHPExcel_Reader_Excel2007();
	            $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
	            $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
	            array_shift($excel_array);  //删除第一个数组(标题);
	            $data = [];
	            $i = 0;
	            foreach($excel_array as $k=>$v) {
	            	$n = $this->tch->numBer($v[0]);
	            	if (!$n) {
	            		$data[$k]['tch_numBer'] = $v[0];
		                $data[$k]['tch_name'] = $v[1];
		                $data[$k]['tch_phone'] = $v[2];
		                $data[$k]['tch_email'] = $v[3];
		                $data[$k]['password'] = md5('gzcj');
	            	}
		            $i++;
	            }
		            $data = $this->arrOnly($data);
	           $success=db('teacher')->insertAll($data); //批量插入数据
	           $error=$i-$success;
	           if ($success) {
		            return  json($res = ['valid' => 1, 'msg' => "共导入{$i}条，成功{$success}条，失败{$error}条。"]);
	           } else {
	        		return  json($res = ['valid' => 0, 'msg' => "共导入{$i}条，成功{$success}条，失败{$error}条。"]);
	           }
	        }else{
	            // 上传失败获取错误信息
			    return	json($res = ['valid' => 0, 'msg' => $file->getError()]);
	        }
	}

	public function edit() {
		$tch_id = input('get.tch_id');
		$oldData = db('teacher')->where('tch_id', $tch_id)->find();
		$this->assign('oldData', $oldData);

		if (request()->isAjax()) {
			$data = input('post.');
			$validate = validate('Add');
			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
				$tch_id = $data['tch_id'];
				$tch_numBer = $data['tch_numBer'];
				$n = db('teacher')->where("tch_id<>'$tch_id' AND tch_numBer='$tch_numBer' ")->select();
				if ($n) {
					$res = ['valid' => 0, 'msg' => '工号不能名称不能重复!'];
				} else{
					unset($data['tch_id']);
					$info = $this->tch->edit($data, $tch_id);
				    if ($info) {
				    	$res = ['valid' => 1, 'msg' => '编辑成功!'];
				    } else {
				    	$res = ['valid' => 0, 'msg' => '编辑失败!'];
				    }
				}
			}
			return json($res);
		}
		return $this->fetch();
	}

	/**
	 * 获取教师列表搜索数据的条数
	 */
	public function checkLen(){
		$tchData = $this->tch->check(input('post.search'));
		return sizeof($tchData);

	}
	/**
	 * 返回搜索数据分页后的数据
	 */
	public function check(){
		$curr = input('post.curr');
		$limit = input('post.limit');
		$search =input('post.search');
		$tchData = $this->tch->check($search);
		$data = array();
		foreach ($tchData as $k => $v) {
			$data[$k]['tch_id'] = $v['tch_id'];
			$data[$k]['tch_numBer'] = $v['tch_numBer'];
			$data[$k]['tch_name'] = $v['tch_name'];
			$data[$k]['tch_phone'] = $v['tch_phone'];
			$data[$k]['tch_email'] = $v['tch_email'];
		}
		$star = ($curr-1)*$limit;
		$data = array_slice($data,$star,$limit);
		return json($data);
	}

	/**
	 * 删除班级的分配(将班级所属教师id改为0)
	 * @return [type] [description]
	 */
	public function del() {
		$tch_id = input('post.');
		foreach ($tch_id as $key => $value) {
			db('class')->where('tch_id', $value)->update(['tch_id' => 0]);
		}
		$res = db('teacher')->delete($tch_id);
		if ($res) {
			return json($res = ['valid' => 1, 'msg' => '删除成功']);
		}
	}


	/**
	 * 查看教师管理的班级
	 */
	public function cla(){
		$tch_id = input('get.id');
		$tch = db('teacher')->where('tch_id', $tch_id)->find();
		$info = $this->Class->getRes($tch_id);
		$this->assign('info', $info);
		$this->assign('tch_id', $tch_id);
		$this->assign('tch_name', $tch['tch_name']);
		return $this->fetch();
	}
	/**
	 * 教师管理班级删除
	 */
	public function classDel(){
		$class_id = input('post.');
		foreach ($class_id as $key => $value) {
			$res = db('class')->where("class_id", $value)->update(['tch_id' => 0]);
		}
		if ($res) {
			return json($res = ['valid' => 0, 'msg' => '删除成功']);
		}
	}
	/**
	 * 班级分配
	 */
	public function classAllot(){
		$info = db('class')->where('tch_id', 0)->select();
		$grade = db('class')->where('tch_id', 0)->order('class_grade desc')->column('class_grade');
		//获取年级(过滤重复的年级)
		foreach ($grade as $key => $v) {
			$gradeArr = $grade;
			unset($gradeArr[$key]);
			if (in_array($grade[$key], $gradeArr)) {
		        unset($grade[$key]);
		    }
		}
		$staffRoom = db('class')->where('tch_id', 0)->column('class_staffRoom');
		foreach ($staffRoom as $key => $v) {
			$staffRoomeArr = $staffRoom;
			unset($staffRoomeArr[$key]);
			if (in_array($staffRoom[$key], $staffRoomeArr)) {
		        unset($staffRoom[$key]);
		    }
		}

		$specialty = db('class')->where('tch_id', 0)->column('class_specialty');

		foreach ($specialty as $key => $v) {
			$specialtyArr = $specialty;
			unset($specialtyArr[$key]);
			if (in_array($specialty[$key], $specialtyArr)) {
		        unset($specialty[$key]);
		    }
		}

		if (request()->isAjax()) {
			return sizeof($info);
		}
		$tch_id = input('get.id');
		$this->assign('tch_id', $tch_id);
		$this->assign('grade', $grade);
		$this->assign('staffRoom', $staffRoom);
		$this->assign('specialty', $specialty);
		return $this->fetch();
	}
	/**
	 *未分配的班级
	 */
	public function select(){
		$data = input('post.');
		$data = $this->Class->getCate($data, 0);
		return json($data);
	}

	/**
	 * 筛选未分配班级数据分页长度
	 */
	public function selectLen(){
		$data = input('post.');
		$data = $this->Class->getCate($data, 0);
		return sizeof($data);
	}
	/**
	 * 筛选未分配班级分页数据
	 * @return [type] [description]
	 */
	public function selectData() {
		$curr = input('get.curr');
		$limit = input('get.limit');
		$star = ($curr-1)*$limit;

		$data = input('post.');
		$specialty = $data[2];

		if ($data[1]) {
			$specialtys = db('class')->where('tch_id', 0)->where('class_staffRoom', $data[1])->column('class_specialty');
		} else {
			$specialtys = db('class')->where('tch_id', 0)->column('class_specialty');
		}

		$data = $this->Class->getCate($data, 0);
		$data = array_slice($data,$star,$limit);

		$res = ["data" => $data, "specialtys" => $specialtys, "specialty" => $specialty];

		return json($res);
	}
	/**
	 * 获取未分配的班级
	 */
	public function selectPage(){
		$curr = input('post.curr');
		$limit = input('post.limit');
		$star = ($curr-1)*$limit;
		$data = db('class')->where('tch_id', 0)->select();
		$data = array_slice($data,$star,$limit);
		return json($data);
	}
	/**
	 * 搜索的数据分页长度
	 */
	public function searchLen() {
		$info = input('post.info');
		$data = $this->Class->searchCla($info);
		return sizeof($data);
	}
	/**
	 * 搜索的数据分页数据
	 */
	public function searchCla() {
		$curr = input('post.curr');
		$limit = input('post.limit');
		$info = input('post.search');
		$star = ($curr-1)*$limit;
		$data = $this->Class->searchCla($info);
		$data = array_slice($data,$star,$limit);
		return json($res);
	}
	/**
	 * 分配班级
	 */
	public function allot(){
		$class_id = input('post.');
		$tch_id = input('get.id');
		foreach ($class_id as $key => $value) {
			$res = db('class')->where("class_id", $value)->update(['tch_id' => $tch_id ]);
		}
		if ($res) {
			return json($res = ['valid' => 0, 'msg' => '删除成功']);
		}
	}

}