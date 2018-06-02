<?php
namespace app\index\controller;

class Index extends Common{
	protected $index;
	protected $company;
	protected $logs;
	protected $signin;
	protected function _initialize() {
		parent::_initialize();
		$this->index = new \app\index\model\Index();
		$this->company = new \app\index\model\Company();
		$this->logs = new \app\index\model\Logs();
		$this->signin = new \app\index\model\Signin();
	}


	/**
	 * 首页
	 */
	public function index() {
		$stu_id = session('stu.stu_id');

		$date = time(); //获取当前时间
		$signTime = $this->signin->getSignIn($stu_id);
		$logsTime = $this->logs->Logs($stu_id);
		// dump($logsTime);exit;
		// $this->signTime($stu_id, $date, $signTime);
		// $this->logsTime($stu_id, $date, $logsTime);


		$info = $this->index->getInfo($stu_id);
		$logsInfo = $this->logs->Logs($stu_id);

		foreach ($logsInfo as $key => $value) {
			$str = $value['logs_content'];
			$str = mb_substr(strip_tags($str),0,strlen($str),'utf-8');
			$value['logs_content'] = $str;
		}
		if (!$info) {
			$info = null;
		}
		$this->notiAnsigns($stu_id);
		$this->assign('info', $info[0]);
		$this->assign('logsInfo', $logsInfo);
		$log = $this->index->getLogsFlag($stu_id);
		$this->assign('log', $log);

		return $this->fetch();
	}

	/**
	 * 实习资料修改和添加页面
	 */
	public function editAdd(){
		$stu_id = session('stu.stu_id');
		$info = $this->index->getInfo($stu_id);
		if ($info) {
			$this->assign('info', $info[0]);
		} else {
			$this->assign('info', null);
		}
		$this->notiAnsigns($stu_id);
		return $this->fetch();
	}
	/**
	 * 实习资料修改
	 */
	public function editInfo() {
		$stu_id = session('stu.stu_id');
		$info = $this->index->getInfo($stu_id);
		$company_id = $info[0]['company_id'];
		$stu_id = session('stu.stu_id');
		$data = input('post.');

		$flag = db('company')->where('stu_id', $stu_id)
							->where('company_name', $data['company_name'])
							->where('company_address', $data['company_address'])
							->where('company_salary', $data['company_salary'])
							->where('company_position', $data['company_position'])
							->where('principal', $data['principal'])
							->where('principal_phone', $data['principal_phone'])
							->find();

		$stuflag = db('student')->where('stu_id', $stu_id)
								->where('stu_phone', $data['stu_phone'])
								->find();

		if (!sizeof($flag) == 0 && !sizeof($stuflag) == 0) {
			return json(['valid' => 0, 'msg' => '修改失败!']);
		}

		$validate = validate('Editadd');
		if(!$validate->check($data)){
			$res = ['valid' => 0, 'msg' => $validate->getError()];
		} else {
			$cres = $this->company->editInfo($data, $company_id);
			$sres = $this->index->editInfo($data, $stu_id);

			if ($cres == 1 || $sres == 1) {
				$res = ['valid' => 1, 'msg' => '修改成功!'];
			} else {
				$res = ['valid' => 0, 'msg' => '修改失败!'];
			}
		}

		return json($res);

	}
	/**
	 * 实习资料添加
	 */
	public function addInfo() {
		$stu_id = session('stu.stu_id');
		$data = input('post.');

		$flag = db('company')->where('stu_id', $stu_id)
							->where('company_name', $data['company_name'])
							->where('company_address', $data['company_address'])
							->where('company_salary', $data['company_salary'])
							->where('company_position', $data['company_position'])
							->where('principal', $data['principal'])
							->where('principal_phone', $data['principal_phone'])
							->find();

		$stuflag = db('student')->where('stu_id', $stu_id)
								->where('stu_phone', $data['stu_phone'])
								->find();

		if (!sizeof($flag) == 0 && !sizeof($stuflag) == 0) {
			return json(['valid' => 0, 'msg' => '添加失败!']);
		}
		$data['stu_id'] = $stu_id;

		$validate = validate('Editadd');
		if(!$validate->check($data)){
			$res = ['valid' => 0, 'msg' => $validate->getError()];
		} else{
			$cres = $this->company->addInfo($data);
			$sres = $this->index->editInfo($data, $stu_id);


			if ($cres == 1 || $sres == 1) {
				$res = ['valid' => 1, 'msg' => '添加成功!'];
			}else {
				$res = ['valid' => 0, 'msg' => '添加失败!'];
			}
		}
		return json($res);
	}

	/**
	 * 日志
	 */
	public function Logs() {
		$stu_id = session('stu.stu_id');
		$this->notiAnsigns($stu_id);
		return $this->fetch('logs');
	}

	/**
	 * 日志的添加
	 */
	public function addlogs(){
		if (request()->isAjax()) {
			$data = input('post.');
			$stu_id = session('stu.stu_id');
			$data['stu_id'] = $stu_id;
			// $data['logs_content'] = $data['logs_content'];

				$time = date('d', time());

				// if ($time >= 1) {
					$res = $this->logs->addLogs($data);
					if ($res) {
						$up= db('student')->where('stu_id', $stu_id)->update(['logsFlag' => 1]);
						$res = ['valid' => 1, 'msg' => '添加成功!'];
					} else {
						$res = ['valid' => 0, 'msg' => '添加失败!'];
					}
				// } else {
				// 	$res = ['valid' => 0, 'msg' => '请在每月的十五号之后填写日志!'];
				// }

			return $res;
		}
	}

	/**
	 * 查看日志
	 */
	public function cLogs() {
		$logs_id =input('param.logs_id');
		$stu_id = session('stu.stu_id');
		db('logs')->where('replyFlag', 1)->where('logs_id', $logs_id)->update(['readFlag' => 1]);

		$oldLogs = $this->logs->oldlogs($logs_id);

		$this->assign('oldLogs', $oldLogs);
		$this->notiAnsigns($stu_id);

		return $this->fetch();
	}

	/**
	 * 签到
	 */
	public function signIn() {
		$stu_id = session('stu.stu_id');
		$signin = $this->index->signInfo($stu_id);//签到提示
		if ($signin) {
			return json($res = ['valid' => 1, 'msg' => '本周已签到!']);
		}
		$address = input('post.address');
		$data=array();
		$data['address'] = $address;
		$data['stu_id'] = $stu_id;
		$res = $this->signin->addSignIn($data);

		if ($res) {
			db('student')->where('stu_id', $stu_id)->update(['signInFlag' => 1]);
			$res = ['valid' => 1, 'msg' => '签到成功!'];
		}else {
			$res = ['valid' => 0, 'msg' => '签到失败!'];
		}

		return json($res);
	}

	/**
	 * 签到前判断是否签到
	 */
	public function sign(){
		$stu_id = session('stu.stu_id');
		$signin = $this->index->signInfo($stu_id);//签到提示
		if ($signin) {
			return json($res = ['valid' => 1, 'msg' => '本周已签到!']);
		} else {
			return json($res = ['valid' => 0, 'msg' => '正在签到,请稍后!']);
		}
	}

	/**
	 * 日志反馈页面
	 */
	public function notice(){
		$stu_id = session('stu.stu_id');

		$logs = $this->logs->notice($stu_id);
		foreach ($logs as $key => $value) {
			$str = $value['logs_reply'];
			$str = mb_substr(strip_tags($str),0,200,'utf-8');
			$value['logs_reply'] = $str;
		}
		$this->assign('logs', $logs);
		$this->notiAnsigns($stu_id);
		return $this->fetch();
	}

	/**
	 * 用户退出
	 */
	public function out() {
		session('stu.stu_id', null);
		return json($res = ['valid' => 1, 'msg' => '已退出当前用户']);
	}

	/**
	 * 日志图片上传
	 */
	public function upload(){
	    $file = request()->file('image');
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file){
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
			    return json($res = ['status' => 1, 'url' =>  '/'. 'uploads'.'/'.$info->getSaveName()]);
	        }else{
	            echo $file->getError();
	        }
	    }
	    return json($res = ['status' => 0, 'msg' => '图片上传错误']);
	}

	public function cnotice(){
		$stu_id = session('stu.stu_id');
		$this->notiAnsigns($stu_id);

		$id = input('param.id');
		db('publicsh')->where('publicsh_id', $id)->update(['readFlag' => 1]);

		$publicsh = db('publicsh')->where('publicsh_id', $id)->find();
		$this->assign('publicsh', $publicsh);
		return $this->fetch();
	}


	public function editPas() {
		$stu_id = session('stu.stu_id');
		$this->notiAnsigns($stu_id);
		if (request()->isAjax()) {
			$stu_id = session('stu.stu_id');
			$data = input('post.');

			$validate = validate('Pas');
			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
				$pas = db('student')->where('stu_id', $stu_id)->value('stu_password');
				if ($pas == md5($data['oldtch_psw'])) {
					if ($data['newtch_psws'] == $data['newtch_psw']) {
						$info = db('student')->where('stu_id', $stu_id)->update(['stu_password' => md5($data['newtch_psws']) ]);

						if ($info) {
					    	$res = ['valid' => 1, 'msg' => '更改成功!'];
					    } else {
					    	$res = ['valid' => 0, 'msg' => '更改失败!'];
					    }
					} else {
						$res = ['valid' => 0, 'msg' => '两次密码不一致!'];
					}
				} else {
					$res = ['valid' => 0, 'msg' => '旧密码错误!'];
				}
			}

			return json($res);
		}
		return $this->fetch();
	}

}