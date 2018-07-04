<?php
namespace app\teacher\controller;

class Classlist extends Common {
	protected $cla;
	protected function _initialize() {
		parent::_initialize();
		$this->cla = new \app\teacher\model\Cla();
	}
	/**
	 * 班级列表
	 */
	public function index() {
		$grade = db('class')->order('class_grade desc')->column('class_grade');
		//获取年级(过滤重复的年级)
		foreach ($grade as $key => $v) {
			$gradeArr = $grade;
			unset($gradeArr[$key]);
			if (in_array($grade[$key], $gradeArr)) {
		        unset($grade[$key]);
		    }
		}
		$specialty = db('class')->column('class_specialty');
		foreach ($specialty as $key => $v) {
			$specialtyArr = $specialty;
			unset($specialtyArr[$key]);
			if (in_array($specialty[$key], $specialtyArr)) {
		        unset($specialty[$key]);
		    }
		}
		$tch_id = db('class')->column('tch_id');
		foreach ($tch_id as $key => $v) {
			$tch_idArr = $tch_id;
			unset($tch_idArr[$key]);
			if (in_array($tch_id[$key], $tch_idArr)) {
		        unset($tch_id[$key]);
		    }
		}


		$tch_name = array();
		foreach ($tch_id as $key => $value) {
			$name= db('teacher')->where('tch_id', $value)->value('tch_name');
			if ($name) {
				$tch_name[$value] = $name;
			}
		}
		$this->assign('grade', $grade);
		$this->assign('specialty', $specialty);
		$this->assign('tch_name', $tch_name);
		return $this->fetch();
	}

	/**
	 * 数组中班级名称唯一的值
	 */
	public function arrOnly ($data){
		$datao = [];
		foreach ($data as $key => $value) {
			$datao[$key] = $value['class_name'];
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
	 * 获取分页总页数
	 */
	public function indexLen() {
		$data = $this->cla->getCla();

        foreach ($data as $key => $value) {
            $sum = db('student')->where('stu_className', $value['class_name'])->select();
            $value['sum'] = sizeof($sum);
            if (!$value['tch_name']) {
                $value['tch_name'] = '<span class="layui-badge">未分配教师</span>';
                $value['tch_phone'] = '<span class="layui-badge">未分配教师</span>';
            }
        }

		$data= $this->arrOnly($data);

		return json($data);
	}

	/**
	 * 获取分页数据
	 */
	public function indexPage() {
		$curr = input('post.curr');
		$limit = input('post.limit');
		$star = ($curr-1)*$limit;
		$data = $this->cla->getCla();
		foreach ($data as $key => $value) {
			$sum = db('student')->where('stu_className', $value['class_name'])->select();
			$value['sum'] = sizeof($sum);
			if (!$value['tch_name']) {
			 	$value['tch_name'] = '<span class="layui-badge">未分配教师</span>';
			 	$value['tch_phone'] = '<span class="layui-badge">未分配教师</span>';
			}
		}
		$data= $this->arrOnly($data);
		$data = array_slice($data,$star,$limit);
		return json($data);
	}

	/**
	 * 班级批量添加
	 * excel 表格导入
	 */
	public function excel() {
		 // import('phpexcel.PHPExcel', EXTEND_PATH);//方法二
	        vendor("PHPExcel.PHPExcel");
	        $objPHPExcel = new \PHPExcel();
	        $file = request()->file('excel');
	        $info = $file->validate(['size'=>80000,'ext'=>'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');
	        if($info){
	            $exclePath = $info->getSaveName();  //获取文件名
	            $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址
	            $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
	            $obj_PHPExcel =$objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
	            $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
	            array_shift($excel_array);  //删除第一个数组(标题);
	            $data = [];
	            $len = sizeof($excel_array);
	            foreach($excel_array as $k => $v) {
	          		 $n = db('class')->where('class_name', $v[1])->select();
	            	if (!$n) {
	            		$data[$k]['class_grade'] = $v[0];
		                $data[$k]['class_name'] = $v[1];
		                $data[$k]['class_staffRoom'] = $v[2];
		                $data[$k]['class_specialty'] = $v[3];
	            	}
	            }

	           	$data = $this->arrOnly($data);

	           $success=db('class')->insertAll($data);
	           $error=$len-$success;
	           if ($success) {
		            return  json($res = ['valid' => 1, 'msg' => "共导入{$len}条，成功{$success}条，失败{$error}条。"]);
	           } else {
		            return  json($res = ['valid' => 0, 'msg' => "共导入{$len}条，成功0条，失败{$error}条。"]);
	           }
	        }else{
			    return	json($res = ['valid' => 0, 'msg' => $file->getError()]);
	        }
	}

	/**
	 * 删除
	 */
	public function del() {
		$class_id = input('post.');
		$class_name = array();
		foreach ($class_id as $va) {
			$class_name[$va] = db('class')->where('class_id', $va)->value('class_name');
		}

		foreach ($class_name as $value) {
			$suc = db('student')->where('stu_className', $value)->delete();
		}
		$res = db('class')->delete($class_id);
		if ($res) {
			return json($res = ['valid' => 1, 'msg' => '删除成功']);
		}
	}

	/**
	 * 搜索数据分页长度
	 * @return [type] [description]
	 */
	public function searchLen() {
		$info = input('post.info');
		$data = $this->cla->search($info);

        foreach ($data as $key => $value) {
            $sum = db('student')->where('stu_className', $value['class_name'])->select();
            $value['sum'] = sizeof($sum);
            if (!$value['tch_name']) {
                $value['tch_name'] = '<span class="layui-badge">未分配教师</span>';
                $value['tch_phone'] = '<span class="layui-badge">未分配教师</span>';
            }
        }

		return json($data);
	}
	/**
	 * 搜索数据分页
	 * @return [type] [description]
	 */
//	public function searchPage() {
//		$curr = input('post.curr');
//		$limit = input('post.limit');
//		$info = input('post.search');
//		$star = ($curr-1)*$limit;
//		$data = $this->cla->search($info);
//		foreach ($data as $key => $value) {
//			$sum = db('student')->where('stu_className', $value['class_name'])->select();
//			$value['sum'] = sizeof($sum);
//			if (!$value['tch_name']) {
//			 	$value['tch_name'] = '<span class="layui-badge">未分配教师</span>';
//			 	$value['tch_phone'] = '<span class="layui-badge">未分配教师</span>';
//			}
//		}
//		$data = array_slice($data,$star,$limit);
//		return json($data);
//	}

	/**
	 * 添加班级
	 */
	public function addClass() {

		if (request()->isAjax()) {
			$data = input('post.');

			$validate = validate('AddClass');
			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
				$n = db('class')->where('class_name', $data['class_name'])->select();
				if ($n) {
					$res = ['valid' => 0, 'msg' => '班级名称不能重复!'];
				} else{
					$info = $this->cla->addClass($data);
				    if ($info) {
				    	$res = ['valid' => 1, 'msg' => '添加成功!'];
				    } else {
				    	$res = ['valid' => 0, 'msg' => '添加失败!'];
				    }
				}
			}
				return json($res);
		}
		return $this->fetch();
	}

	public function edit() {
		$class_id = input('get.class_id');
		$oldData = db('class')->where('class_id', $class_id)->find();
		$this->assign('oldData', $oldData);

		if (request()->isAjax()) {
			$data = input('post.');
			$validate = validate('AddClass');
			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
				$class_id = $data['class_id'];
				$class_name = $data['class_name'];
				$n = db('class')->where("class_id<>'$class_id' AND class_name='$class_name' ")->select();
				if ($n) {
					$res = ['valid' => 0, 'msg' => '班级名称不能重复!'];
				} else{
					unset($data['class_id']);
					$info = $this->cla->edit($data, $class_id);
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

	public function selectLen() {
		$data = input('post.');
		$specialty = $data[2];

		if ($data[1]) {
			$specialtys = db('class')->where('class_staffRoom', $data[1])->column('class_specialty');
		} else {
			$specialtys = db('class')->column('class_specialty');
		}

		if (!in_array($specialty, $specialtys)) {
		  	$data[2] = '';
		}

		$data = $this->cla->getCate($data, 1);
		return sizeof($data);
	}

	public function selectData() {
		$curr = input('get.curr');
		$limit = input('get.limit');
		$data = input('post.');

		$specialty = $data[2];


		if ($data[1]) {
			$specialtys = db('class')->where('class_staffRoom', $data[1])->column('class_specialty');
		} else {
			$specialtys = db('class')->column('class_specialty');
		}

		if (!in_array($specialty, $specialtys)) {
		  	$data[2] = '';
		}

		$star = ($curr-1)*$limit;
		$data = $this->cla->getCate($data, 1);
		foreach ($data as $key => $value) {
			$sum = db('student')->where('stu_className', $value['class_name'])->select();
			$value['sum'] = sizeof($sum);
			if (!$value['tch_name']) {
			 	$value['tch_name'] = '<span class="layui-badge">未分配教师</span>';
			 	$value['tch_phone'] = '<span class="layui-badge">未分配教师</span>';
			}
		}
		$data = array_slice($data,$star,$limit);

		foreach ($specialtys as $key => $v) {
			$specialtysArr = $specialtys;
			unset($specialtysArr[$key]);
			if (in_array($specialtys[$key], $specialtysArr)) {
		        unset($specialtys[$key]);
		    }
		}

		$res = ["data" => $data, "specialtys" => $specialtys, "specialty" => $specialty];

		return json($res);
	}
}