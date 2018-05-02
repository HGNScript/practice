<?php
namespace app\admin\controller;

/**
*c x
*/
class Article extends Common {
	protected $db;
	protected function _initialize() {
		parent::_initialize();
		$this->db = new \app\admin\model\Article();
	}

	public function index() {
		return $this->fetch();
	}

	public function getData() {
		$res = $this->db->getres();
		return json($res);
	}

	public function addArc(){
		$cateData = db('cate')->select();
		$tagData = db('tag')->select();
		$this->assign('cateData', $cateData);
		$this->assign('tagData', $tagData);
		$arc_id = null;

		if (request()->isPost()) {
			$res = $this->db->addArc(input('post.'), $arc_id);

			if ($res['valid']) {
				$this->success($res['msg'],'Article/index');
			} else {
				$this->error($res['msg']);
			}
		}
		return $this->fetch();
	}
	public function eidtArc() {
		$arc_id = input('get.arc_id');
		$oldData = $this->db->oldData($arc_id);
		$cateData = db('cate')->select();
		$tagData = db('tag')->select();
		$arcTag = $this->db->arcTag($arc_id);
		$this->assign('arcTag', $arcTag);
		$this->assign('cateData', $cateData);
		$this->assign('tagData', $tagData);
		$this->assign('oldData', $oldData);

		if (request()->isPost()) {
			$res = $this->db->addArc(input('post.'), $arc_id);

			if ($res['valid']) {
				$this->success($res['msg'],'Article/index');
			} else {
				$this->error($res['msg']);
			}
		}
		return $this->fetch();
	}
	public function recycle() {
		$arc_id = input('get.arc_id');
		$id = input('post.');
		if ($arc_id) {
			$res = db('arc')->where('arc_id', $arc_id)->update(['is_recycle' => 0]);
		} else {
			foreach ($id as $key => $v) {
				$res = db('arc')->where('arc_id', $id[$key])->update(['is_recycle' => 0]);
			}
		}

		if ($res) {
			$this->success('删除成功!','Article/index');
		}
	}

}