<?php

namespace app\index\controller;
use think\Controller;
use think\Request;

class Common extends Controller {
    public function __construct ( Request $request = null ) {
		parent::__construct( $request );
		if(!session('stu.stu_id')) {
			$this->redirect('teacher/login/index');
		}
	}

	/**
	 * 获取当前是否签到, 获取通知条数
	 */
	public function notiAnsigns($stu_id){
		$logs = $this->logs->notice($stu_id);//通知提示

		$notice = sizeof($logs);

		$signin = $this->index->signInfo($stu_id);//签到提示
		if (!$signin) {
			$this->assign('signin', 1);
		} else {
			$this->assign('signin', null);
		}
		$this->assign('notice', $notice);
	}

	public function signTime($stu_id, $date, $signTime) {
		if (sizeof($signTime) > 0) {
			$signTime =  $signTime[0]['sendtime'];
			$da = date("w",time());//获取当前星期几

			$h = date('h', time());
			$i = date('i', time());
			$s = date('h', time());

			if ($da != 0) {
				$mixDate = $date - ($da - 1)*24*60*60;
				$mixDate = $mixDate-$h*60*60-$i*60-$s;
				if ($signTime >= $mixDate) {
					$this->index->signInFlag($stu_id, 1);
				} else{
					$this->index->signInFlag($stu_id, 0);
				}
			} else {
				$da = 7;
				$mixDate = $date - ($da - 1)*24*60*60;
				$mixDate = $mixDate-$h*60*60-$i*60-$s;
				if ($signTime >= $mixDate) {
					$this->index->signInFlag($stu_id, 1);
				} else{
					$this->index->signInFlag($stu_id, 0);
				}
			}
		} else {
			$this->index->signInFlag($stu_id, 0);
		}
	}

	public function logsTime($stu_id, $date, $logsTime) {
		if ( sizeof($logsTime) > 0) {
			$logsTime =  $logsTime[0]['sendtime'];
			$da = date("d", time());//获取当前多少号

			$h = date('h', time());
			$i = date('i', time());
			$s = date('h', time());
			if ($da >= 15) {
				$mixDate = $date - ($da - 15)*24*60*60;
				$mixDate = $mixDate-$h*60*60-$i*60-$s;
				if ($logsTime >= $mixDate) {
					$this->index->writeFlag($stu_id, 1);
				} else{
					$this->index->writeFlag($stu_id, 0);
				}
			} else {
				$days = cal_days_in_month(CAL_GREGORIAN, date('m', time()), date('Y', time()));
				$mixDate = $date - ($da+($days-15))*24*60*60;
				$mixDate = $mixDate-$h*60*60-$i*60-$s;
				if ($logsTime >= $mixDate) {
					$this->index->writeFlag($stu_id, 1);
				} else{
					$this->index->writeFlag($stu_id, 0);
				}
			}
		} else {
			$this->index->writeFlag($stu_id, 0);
			// dump('未写');exit;
		}
	}
}