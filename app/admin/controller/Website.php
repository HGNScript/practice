<?php
namespace app\admin\controller;

class Website extends Common {

	protected $db;
	protected function _initialize () {
		parent::_initialize();
		// $this->db = new \app\admin\model\Tag();
	}
	public function index() {
		return $this->fetch();
	}

	public function getData() {
		$res = db('website')->select();
		return json($res);
	}
	public function addWeb() {
		if (request()->isPost()) {
				$res = db('website')->insert(input('post.'));
				if ($res) {
					$this->success('添加成功!','Website/index');
				} else {
					$this->error('添加失败!');
				}
			}
		return $this->fetch();
	}
	public function eidtWeb() {
			$web_id = input("get.web_id");
			$oldData = db('website')->where('web_id', $web_id)->find();
			$this->assign('oldData', $oldData);

			if (request()->isPost()) {
				$res = db('website')
				    ->where('web_id', $web_id)
				    ->update([
				        'web_name'  => input('post.web_name'),
				        'web_value' => input('post.web_value'),
				    ]);
				if ($res) {
					$this->success('编辑成功!','Website/index');
				}
			}
			return $this->fetch();
	}

}