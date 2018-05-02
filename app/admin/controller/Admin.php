<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Admin extends Common {
	protected $db;
	protected function _initialize () {
		parent::_initialize();
		$this->db = new \app\admin\model\Admin();
	}
	public function adminManage() {
		return $this->fetch();
	}

	public function getData() {
		$adminInfo = Db('admin')->select();
		return json($adminInfo);
	}

	public function outAdmin() {
		session('admin', null);
		$this->success('退出成功!', 'admin/login/index');
	}

	public function addAdmin() {
		if (request()->isPost()) {
			$res = $this->db->addAdmin(input("post."));

			if ($res['valid']) {
				$this->success($res['msg'],'Admin/adminManage');
			} else {
				$this->error($res['msg']);
			}
		}
		// $olddata = Db('admin')->

		return $this->fetch();
	}
	public function eidtAdmin() {
		$admin_id = input('param.admin_id');
		$oldData = Db('admin')->where('admin_id', $admin_id)->find();

		$this->assign('admin_username', $oldData['admin_username']);

		if (request()->isPost()) {
			$res = $this->db->eidtAdmin(input('post.'), $admin_id);

			if ($res['valid']) {
				$this->success($res['msg'],'Admin/adminManage');
			} else {
				$this->error($res['msg']);
			}
		}

		return $this->fetch();
	}
	public function del() {
	 	$id = input('post.id');
	 	db('admin')->delete($id);
	}
}
