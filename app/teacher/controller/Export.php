<?php
namespace app\teacher\controller;

class Export extends Common {
	protected $cla;
	protected $stu;
	protected function _initialize() {
		parent::_initialize();
		$this->cla = new \app\teacher\model\Cla();
		$this->stu = new \app\teacher\model\Stu();
		$this->tch = new \app\teacher\model\Index();
	}

	public function index() {
		$tch_id = session('tch.tch_id');
		$authority = $this->tch->getAuthority($tch_id);


		if ($authority == 1) {
			// $classinfo = db('class')->select();

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

			$this->assign('grade', $grade);
			$this->assign('specialty', $specialty);

		} else {

			$classinfo = db('class')->where('tch_id', $tch_id)->select();
			foreach ($classinfo as $key => $value) {
				$sum = db('student')->where('stu_className', $value['class_name'])->select();
				$classinfo[$key]['class_sum']= sizeof($sum);
			}
			$this->assign('classinfo', $classinfo);

		}


		if (request()->isAjax()) {
			$curr = input('get.curr');
			$limit = input('get.limit');
			$search = input('post.search');
			$select = input('post.');

			if ($select) {
				if($search) {
					$classinfo = db('class')->where("class_grade like '%$search%' OR class_name like '%$search%' OR class_staffRoom like '%$search%' OR class_specialty like '%$search%'")->order('class_grade desc')->select();
				} else {
					$classinfo = $this->cla->exportClass($select);
				}

			} else {
				$classinfo = db('class')->order('class_grade desc')->select();
			}



			if ($curr) {
				$star = ($curr-1)*$limit;
				$classinfo = array_slice($classinfo,$star,$limit);
				foreach ($classinfo as $key => $value) {
					$sum = db('student')->where('stu_className', $value['class_name'])->select();
					$classinfo[$key]['class_sum']= sizeof($sum);
				}
				return json($classinfo);
			} else {
				return sizeof($classinfo);
			}
		}

		$this->assign('authority', $authority);
		return $this->fetch();
	}

	public function excel() {

		$class_id = input('get.class_id');

		vendor("PHPExcel.PHPExcel");

	    $objPHPExcel = new \PHPExcel();

	    $objPHPExcel->getProperties();

	    $class_name = db('class')->where('class_id', $class_id)->value('class_name');

	    $stuData = $this->stu->expot($class_name);

	    $header=['学号','名称','实习评分','年级','班级名称','教研室','专业','联系电话','身份证号码','班主任名称','班主任联系电话','跟班教师','跟班教师联系电话','实习单位名称','实习单位地址','实习岗位'];

	    $key = ord("A"); // 设置表头

	    foreach ($header as $v) {
	        $colum = chr($key);

	        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

	        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

	        $key += 1;

	    }

	    $column = 2;
	    $objActSheet = $objPHPExcel->getActiveSheet();

	    foreach ($stuData as $key => $rows) { // 行写入
	    	$arr = [];
	    	array_push($arr, '\''.$rows['stu_numBer']);
	    	array_push($arr, $rows['stu_name']);
	    	array_push($arr, $rows['stu_score']);
	    	array_push($arr, $rows['class_grade']);
	    	array_push($arr, $rows['stu_className']);
	    	array_push($arr, $rows['class_staffRoom']);
	    	array_push($arr, $rows['class_specialty']);
	    	array_push($arr, '\''.$rows['stu_phone']);
	    	array_push($arr, '\''.$rows['identity']);
	    	array_push($arr, $rows['classteacher']);
	    	array_push($arr, '\''.$rows['classteacher_phone']);
	    	array_push($arr, $rows['tch_name']);
	    	array_push($arr, '\''.$rows['tch_phone']);
	    	array_push($arr, $rows['company_name']);
	    	array_push($arr, $rows['company_address']);
	    	array_push($arr, $rows['company_position']);

	    	// dump($rows);exit;

	        $span = ord("A");

	        foreach ($arr as $keyName => $value) { // 列写入

	            $objActSheet->setCellValue(chr($span) . $column, $value);

	            $span++;

	        }

	        $column++;

	    }


	    $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表

	    $fileName = $class_name .'.xls';

	    header('Content-Disposition: attachment;filename='.$fileName);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

	    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	    $objWriter->save('php://output'); // 文件通过浏览器下载

	}
}