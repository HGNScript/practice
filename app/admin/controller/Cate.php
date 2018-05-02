<?php
namespace app\admin\controller;

class Cate extends Common {

	protected $db;
	protected function _initialize () {
		parent::_initialize();
		$this->db = new \app\admin\model\Cate();
	}
	public function index() {
		return $this->fetch();
	}

	public function getData() {
		$res = $this->db->getres();
		return json($res);
	}

	public function addCate() {
		if (request()->isPost()) {
			$res = $this->db->addCate(input('post.'));
			if ($res['valid']) {
				$this->success($res['msg'],'Cate/index');
			} else {
				$this->error($res['msg']);
			}
		}
		return $this->fetch();
	}

	public function eidtCate() {
		$cate_id = input("get.cate_id");
		$oldData = $this->db->oldData($cate_id);

		$this->assign('oldData', $oldData);

		if (request()->isPost()) {
			$data =input('post.');
			$res = $this->db->eidtCate($data, $cate_id);
			if ($res['valid']) {
				$this->success($res['msg'],'Cate/index');
			} else {
				$this->error($res['msg']);
			}
		}
		return $this->fetch();
	}
	public function del() {
	 	$id = input('post.id');
	 	db('cate')->delete($id);
	}
}