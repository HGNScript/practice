<?php
namespace app\admin\model;

use think\Model;

class Cate extends Model {

	protected $pk = "cate_id";
	protected $table = "blog_cate";

	public function getres() {
		$res = $this->order('cate_sort desc, cate_id')->select();
		foreach ($res as $item) {
			$pcate = $this->where('cate_id', $item['cate_pid'])->field('cate_name')->find();
			$item['p_name'] = $pcate['cate_name'];
		}
		return $res;
	}
	public function addCate($data) {
		$validate = validate('AddCate');
		if (!$validate->check($data)) {
			return $res = ['valid' => 0, 'msg' => $validate->getError()];
		} else {
			$exist = $this->where('cate_name', $data['cate_name'])->find();
			if ($exist) {
				return $res = ['valid' => 0, 'msg' => '分类名称已存在!'];
			} else {
				$info = $this->save($data);
				if ($info) {
					return $res = ['valid' => 1, 'msg' => '添加成功!'];
				} else {
					return $res = ['valid' => 0, 'msg' => '添加失败!'];
				}
			}
		}
	}
	public function oldData($cate_id) {
		$oldData = $this->where('cate_id', $cate_id)->find();
		$pcate = $this->where('cate_id', $oldData['cate_pid'])->field('cate_name')->find();
		$oldData['p_name'] = $pcate['cate_name'];
		return $oldData;
	}
	public function eidtCate($data, $cate_id) {
		$info = $this->save($data, ['cate_id' => $cate_id]);
		if ($info) {
			return $res = ['valid' => 1, 'msg' => '编辑成功!'];
		} else {
			return $res = ['valid' => 0, 'msg' => '编辑失败!'];
		}
	}
}