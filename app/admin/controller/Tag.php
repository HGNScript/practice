<?php
namespace app\admin\controller;

class Tag extends Common {

	protected $db;
	protected function _initialize () {
		parent::_initialize();
		$this->db = new \app\admin\model\Tag();
	}
	public function index() {
		return $this->fetch();
	}

	public function getData() {
		$res = $this->db->getres();
		return json($res);
	}

	public function addTag() {
		if (request()->isPost()) {
			$res = $this->db->addTag(input('post.'));
			if ($res['valid']) {
				$this->success($res['msg'],'Tag/index');
			} else {
				$this->error($res['msg']);
			}
		}
		return $this->fetch();
	}

	public function eidtTag() {
		$tag_id = input("get.tag_id");
		$oldData = $this->db->oldData($tag_id);

		$this->assign('oldData', $oldData);

		if (request()->isPost()) {
			$data =input('post.');
			$res = $this->db->eidtTag($data, $tag_id);
			if ($res['valid']) {
				$this->success($res['msg'],'Tag/index');
			} else {
				$this->error($res['msg']);
			}
		}
		return $this->fetch();
	}
	public function del() {
	 	$id = input('post.id');
	 	db('tag')->delete($id);
	}
}