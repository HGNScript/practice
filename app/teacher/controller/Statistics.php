<?php
namespace app\teacher\controller;

use app\teacher\model\Stu;
use think\Controller;

class Statistics extends Common{
	protected $cla;
	protected $tch;
	protected function _initialize() {
		parent::_initialize();
		$this->tch = new \app\teacher\model\Index();
		$this->cla = new \app\teacher\model\Cla();
	}

	/**
	 * 统计页面
	 * @return [type] [description]
	 */
	public function index() {
		$tch_id = session('tch.tch_id');
		$authority = $this->tch->getAuthority($tch_id);

		if ($authority == 1) {
			$staffRoom = input('get.class_staffRoom');
			/*一级管理员时,获取教研室班级数据*/
			if($staffRoom) {

				if(request()->isAjax()){
					$staffRoom = input('get.class_staffRoom');
					$search = input('post.search');

					if ($search) {
						$res = db('class')->where("class_staffRoom='$staffRoom' AND class_name like '%$search%'")->order('class_grade desc')->column('class_name');
					} else {
						$res = db('class')->where('class_staffRoom', $staffRoom)->column('class_name');
					}



					$data = array();
					foreach ($res as $key => $value) {
						$sum = db('student')->where('stu_className', $value)->select();
						$signin =db('student')->where('stu_className', $value)->where('signInFlag', 1)->select();
						$unSignin = sizeof($sum) - sizeof($signin);

						$logs =db('student')->where('stu_className', $value)->where('logsFlag', 1)->select();
                        $unLogs = sizeof($sum) - sizeof($logs);

                        $company = null;
                        $uncompany = null;

                        $uncompanys = db('student')->alias('s')->join('practice_company c','s.stu_id = c.stu_id','LEFT')
                            ->where('stu_className',$value)->where("company_name is null")->select();
                        $uncompany+=sizeof($uncompanys);
                        $company = sizeof($sum) - $uncompany;

						$data[$key]['class_name'] =$value;

						$data[$key]['signin'] =sizeof($signin);
						$data[$key]['unSignin'] = $unSignin;
						$data[$key]['logs'] =sizeof($logs);
						$data[$key]['unLogs'] =$unLogs;
                        $data[$key]['company'] =$company;
                        $data[$key]['uncompany'] =$uncompany;
                        $data[$key]['sum'] = sizeof($sum);

						if ($data[$key]['signin'] == 0) {
							$data[$key]['signin'] = '<span class="layui-badge layui-bg-green">'. $data[$key]['signin'] .'</span>';
						} else {
							$data[$key]['signin'] = '<span class="layui-badge layui-bg-green">'.$data[$key]['signin'].'('. floor($data[$key]['signin']/$data[$key]['sum']*100) .'%)</span>';
						}

						if ($data[$key]['unSignin'] == 0) {
							$data[$key]['unSignin'] = '<span class="layui-badge">'. $data[$key]['unSignin'] .'</span>';
						} else {
							$data[$key]['unSignin'] = '<span class="layui-badge">'. $data[$key]['unSignin'].'('. ceil($data[$key]['unSignin']/$data[$key]['sum']*100) .'%)</span>';
						}

						if ($data[$key]['logs'] == 0) {
							$data[$key]['logs'] = '<span class="layui-badge layui-bg-green">'. $data[$key]['logs'] .'</span>';
						} else {
							$data[$key]['logs'] = '<span class="layui-badge layui-bg-green">'. $data[$key]['logs'].'('. round($data[$key]['logs']/$data[$key]['sum']*100) .'%)</span>';
						}

						if ($data[$key]['unLogs'] == 0) {
							$data[$key]['unLogs'] = '<span class="layui-badge">'. $data[$key]['unLogs'] .'</span>';
						} else {
							$data[$key]['unLogs'] = '<span class="layui-badge">'. $data[$key]['unLogs'].'('. round($data[$key]['unLogs']/$data[$key]['sum']*100) .'%)</span>';
						}
                        if ($data[$key]['company'] == 0) {
                            $data[$key]['company'] = '<span class="layui-badge">'. $data[$key]['company'] .'</span>';
                        } else {
                            $data[$key]['company'] = '<span class="layui-badge">'. $data[$key]['company'].'('. round($data[$key]['company']/$data[$key]['sum']*100) .'%)</span>';
                        }
                        if ($data[$key]['uncompany'] == 0) {
                            $data[$key]['uncompany'] = '<span class="layui-badge">'. $data[$key]['uncompany'] .'</span>';
                        } else {
                            $data[$key]['uncompany'] = '<span class="layui-badge">'. $data[$key]['uncompany'].'('. round($data[$key]['uncompany']/$data[$key]['sum']*100) .'%)</span>';
                        }
						$data[$key]['sum'] = '<span class="layui-badge layui-bg-green">'.$data[$key]['sum'] .'</span>';

					}

                    return json($data);
				}


				$this->assign('staffRoom', $staffRoom);
			}
				$this->assign('staffRoom', $staffRoom);
		} else {

			$class_name = db('class')->where('tch_id', $tch_id)->column('class_name');
			$data = array();
				foreach ($class_name as $key => $value) {
					$sum = db('student')->where('stu_className', $value)->select();
					$signin =db('student')->where('stu_className', $value)->where('signInFlag', 1)->select();
					$unSignin = sizeof($sum) - sizeof($signin);

					$logs =db('student')->where('stu_className', $value)->where('logsFlag', 1)->select();
					$unLogs = sizeof($sum) - sizeof($logs);
                    $company = null;
                    $uncompany = null;

                    $uncompanys = db('student')->alias('s')->join('practice_company c','s.stu_id = c.stu_id','LEFT')
                        ->where('stu_className',$value)->where("company_name is null")->select();
                    $uncompany+=sizeof($uncompanys);
                    $company = sizeof($sum) - $uncompany;

					$data[$key]['class_name'] =$value;
					$data[$key]['signin'] =sizeof($signin);
					$data[$key]['unSignin'] = $unSignin;
					$data[$key]['logs'] =sizeof($logs);
					$data[$key]['unLogs'] =$unLogs;
                    $data[$key]['company'] =$company;
                    $data[$key]['uncompany'] =$uncompany;
                    $data[$key]['sum'] =sizeof($sum);

                    $this->assign('data', $data);

                }
				$this->assign('staffRoom', null);

		}

		/**
		 * 一级管理员的首页,获取年级数据
		 */
		$grade = db('class')->order('class_grade desc')->column('class_grade');
		foreach ($grade as $key => $v) {
			$gradeArr = $grade;
			unset($gradeArr[$key]);
			if (in_array($grade[$key], $gradeArr)) {
		        unset($grade[$key]);
		    }
		}
		$gradeData =array();
		$nubs = null;
		$signins = null;
		$logss = null;
        $company = null;
        $uncompany = null;
		foreach ($grade as $key => $value) {
			$className = db('class')->where('class_grade', $value)->column('class_name');
				foreach ($className as $k => $va) {
					$nub = db('student')->where('stu_className', $va)->select();
						$signin = db('student')->where('stu_className', $va)->where('signInFlag', 1)->select();
						$logs = db('student')->where('stu_className', $va)->where('logsFlag', 1)->select();

						$nubs += sizeof($nub);
						$signins += sizeof($signin);
						$unsignin = $nubs - $signins;

						$logss += sizeof($logs);
						$unLogss = $nubs - $logss;

                        $uncompanys = db('student')->alias('s')->join('practice_company c','s.stu_id = c.stu_id','LEFT')
                              ->where('stu_className',$va)->where("company_name is null")->select();
                         $uncompany+=sizeof($uncompanys);
                         $company = $nubs - $uncompany;

						$gradeData[$key]['class_grade'] =$value;
						$gradeData[$key]['signin'] =$signins;
						$gradeData[$key]['unSignin'] = $unsignin;
						$gradeData[$key]['logs'] =$logss;
						$gradeData[$key]['unLogs'] = $unLogss;
                        $gradeData[$key]['company'] = $company;
                         $gradeData[$key]['uncompany'] = $uncompany;
						$gradeData[$key]['sum'] = $nubs;
				}
					$nubs = null;
					$signins = null;
					$logss = null;
		}


		$this->assign('authority', $authority);
		$this->assign('gradeData', $gradeData);
		return $this->fetch();
	}

	/**
	 * 统计图数据
	 * data 签到人数, 未签到人数, 日志填写人数, 日志为填写人数 ,实习人数，未实习人数
	 */
	public function proportion(){
		$staffRoom = input('get.class_staffRoom');
		/**
		 * 获取教研室数据
		 */
		if($staffRoom) {
			$res = db('class')->where('class_staffRoom', $staffRoom)->column('class_name');
            $data = array();
			$sums = null;
			$signins = null;
			$logss = null;
			$company = null;
			$uncompany = null;

            foreach ($res as $key => $value) {
				$sum = db('student')->where('stu_className', $value)->select();
				$signin =db('student')->where('stu_className', $value)->where('signInFlag', 1)->select();
				$unsignins = null;

				$logs =db('student')->where('stu_className', $value)->where('logsFlag', 1)->select();
				$unLogss = null;

                $uncompanys = db('student')->alias('s')->join('practice_company c','s.stu_id = c.stu_id','LEFT')
                    ->where('stu_className',$value)->where("company_name is null")->select();



				$sums += sizeof($sum);
				$signins += sizeof($signin);
				$unsignins += $sums - $signins;
				$logss += sizeof($logs);
				$unLogss += $sums - $logss;
                $uncompany+=sizeof($uncompanys);
                $company = $sums - $uncompany;

                $data['signin'] =$signins;
				$data['unSignin'] = $unsignins;
				$data['logs'] = $logss;
				$data['unLogs'] =$unLogss;
				$data['company'] = $company;
				$data['uncompany'] = $uncompany;
			}
            return json($data);
		} else {
			$tch_id = session('tch.tch_id');
			$authority = $this->tch->getAuthority($tch_id);
			if ($authority == 2) {
				$data = array();
				$sums = null;
				$signins = null;
				$logss = null;
                $company = null;
                $uncompany = null;
				$className = db('class')->where('tch_id', $tch_id)->column('class_name');

                foreach ($className as $key => $value) {
					$sum = db('student')->where('stu_className', $value)->select();
					$signin =db('student')->where('stu_className', $value)->where('signInFlag', 1)->select();
					$unsignins = null;

					$logs =db('student')->where('stu_className', $value)->where('logsFlag', 1)->select();
					$unLogss = null;

					$sums += sizeof($sum);
					$signins += sizeof($signin);
					$unsignins += $sums - $signins;
					$logss += sizeof($logs);
					$unLogss += $sums - $logss;

                    $uncompanys = db('student')->alias('s')->join('practice_company c','s.stu_id = c.stu_id','LEFT')
                        ->where('stu_className',$value)->where("company_name is null")->select();
                    $uncompany+=sizeof($uncompanys);
                    $company = $sums - $uncompany;

					$data['signin'] =$signins;
					$data['unSignin'] = $unsignins;
					$data['logs'] = $logss;
					$data['unLogs'] =$unLogss;
                    $data['company'] = $company;
                    $data['uncompany'] = $uncompany;
				}
				return json($data);
			}
			$sum = db('student')->select();
			$signin = db('student')->where('signInFlag', 1)->select();
			$unSignin = sizeof($sum) - sizeof($signin);
			$data =array('signin' => sizeof($signin), 'unSignin' => $unSignin);

			$logs = db('student')->where('logsFlag', 1)->select();
			$unLogs = sizeof($sum) - sizeof($logs);
			$data['logs'] = sizeof($logs);
			$data['unLogs'] = $unLogs;

            $uncompanys = db('student')->alias('s')->join('practice_company c','s.stu_id = c.stu_id','LEFT')
                        ->where("company_name is null")->select();
            $uncompany = sizeof($uncompanys);
            $company = sizeof($sum) - $uncompany;
            $data['company'] = $company;
            $data['uncompany'] = $uncompany;
            return json($data);
		}
	}

	//获取教师管理的班级所有学生信息
	public function allStuInfo(){
        $tch_id = session('tch.tch_id');

        $search = input('post.search');

        $authority = db('teacher')->where('tch_id', '=', $tch_id)->value('authority');

        $stuData = (new Stu())->getAllStuInfo($tch_id, $search, $authority);

        return json($stuData);
    }
}