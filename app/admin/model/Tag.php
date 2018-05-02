<?php
namespace app\admin\model;

use think\Model;

class Tag extends Model {

	protected $pk = "tag_id";
	protected $table = "blog_tag";

	public function getres() {
		return $this->order('tag_sort desc, tag_id')->select();
	}

	public function addTag($data) {
		$validate = validate('AddTag');
		if (!$validate->check($data)) {
			return $res = ['valid' => 0, 'msg' => $validate->getError()];
		} else {
			$exist = $this->where('tag_name', $data['tag_name'])->find();
			if ($exist) {
				return $res = ['valid' => 0, 'msg' => '标签名称已存在!'];
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
	public function oldData($tag_id) {
		$oldData = $this->where('tag_id', $tag_id)->find();
		return $oldData;
	}
	public function eidtTag($data, $tag_id) {
		dump($tag_id);
		$info = $this->save($data, ['tag_id' => $tag_id]);
		if ($info) {
			return $res = ['valid' => 1, 'msg' => '编辑成功!'];
		} else {
			return $res = ['valid' => 0, 'msg' => '编辑失败!'];
		}
	}
}