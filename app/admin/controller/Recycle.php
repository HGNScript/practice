<?php
namespace app\admin\controller;


class Recycle extends Common {

	protected $db;
	protected function _initialize() {
		parent::_initialize();
		$this->db = new \app\admin\model\Recycle();
	}
	public function index() {
		return $this->fetch();
	}
	public function getData() {
		$res = $this->db->getres();
		return json($res);
	}
	public function recycle() {
		$id = input('post.');
		foreach ($id as $key => $v) {
			$res = db('arc')->where('arc_id', $id[$key])->update(['is_recycle' => 1]);
		}

		if ($res) {
			$this->success('恢复成功!','Recycle/index');
		}
	}

}