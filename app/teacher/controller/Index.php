<?php
namespace app\teacher\controller;

class Index extends Common {
	protected $tch;
	protected $adm;
	protected function _initialize() {
		parent::_initialize();
		$this->tch = new \app\teacher\model\Index();
		$this->adm = new \app\teacher\model\Admin();
	}
	/**
	 * 首页
	 */
	public function index() {
		$tch_id = session('tch.tch_id');
		$authority = $this->tch->getAuthority($tch_id);
		if ($authority == 2) {
			$classData = db('class')->where('tch_id', $tch_id)->select();
			if ($classData) {
				$this->assign('classData', $classData);
			} else {
				$this->assign('classData', null);
			}
		}
		if ($authority == 1) {
			$staffRoom = db('class')->column('class_staffRoom');
			foreach ($staffRoom as $key => $v) {
				$staffRoomeArr = $staffRoom;
				unset($staffRoomeArr[$key]);
				if (in_array($staffRoom[$key], $staffRoomeArr)) {
			        unset($staffRoom[$key]);
			    }
			}

			$this->assign('staffRoom', $staffRoom);
			$this->assign('classData', null);
		}
		$this->assign('authority', $authority);
		return $this->fetch();
	}

	/**
	 * 退出登录
	 */
	public function out() {
		session('tch.tch_id', null);
		$this->redirect('Login');
	}

	public function editInfo(){
		$tch_id = session('tch.tch_id');
		$tchInfo = db('teacher')->where('tch_id', $tch_id)->find();
		$this->assign('tchInfo', $tchInfo);

		if (request()->isAjax()) {
			$data = input('post.');
			$tch_id = session('tch.tch_id');
			$validate = validate('Add');
			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
				$tch_numBer = $data['tch_numBer'];
				$n = db('teacher')->where("tch_id<>'$tch_id' AND tch_numBer='$tch_numBer' ")->select();
				if ($n) {
					$res = ['valid' => 0, 'msg' => '工号不能名称不能重复!'];
				} else{
					$info = $this->adm->edit($data, $tch_id);

					session('tch', ['tch_id' => $tch_id, 'tch_name' => $data['tch_name']]);
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
	public function editPas() {
		if (request()->isAjax()) {
			$tch_id = session('tch.tch_id');
			$data = input('post.');

			$validate = validate('Editpsw');
			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
				$pas = db('teacher')->where('tch_id', $tch_id)->value('password');

				if ($pas == md5($data['oldtch_psw'])) {
					if ($data['newtch_psws'] == $data['newtch_psw']) {
						$info = db('teacher')->where('tch_id', $tch_id)->update(['password' => md5($data['newtch_psws']) ]);

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