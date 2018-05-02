<?php
namespace app\admin\model;
use think\Model;

class Admin extends Model {
	protected $pk = 'admin_id';
	protected $table = 'blog_admin';

	protected function setAdminPasswordAttr($value)
	{
		return md5($value);
	}

	/**
	 * 管理员添加
	 * @param [type] $data [description]
	 */
	public function addAdmin($data){
		$validate = validate('AddAdmin');
		if (!$validate->check($data)) {
			return $res = ['valid' => 0, 'msg' => $validate->getError()];
		} else {
			$exist = $this->where('admin_username', $data['admin_username'])->find();
			if ($exist) {
				return $res = ['valid' => 0, 'msg' => '管理员名称已存在,请更换名称!'];
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

	/**
	 * 管理员的编辑
	 * @return [type] [description]
	 */
	public function eidtAdmin($data, $admin_id){
		$validate = validate('EidtAdmin');
		dump($data);
		if (!$validate->check($data)) {
			return $res = ['valid' => 0, 'msg' => $validate->getError()];
		} else {
			if ($data['admin_password'] != $data['newpassword']) {
				dump(1);
				return $res = ['valid' => 0, 'msg' => '两次密码不一致!'];
			} else {
				$oldinfo = $this->where('admin_id', $admin_id)->where('admin_password', $data['oldpassword'])->find();
				if ($oldinfo) {
					$this->allowField(true)->save($data,['admin_id' => $admin_id]);
					return $res = ['valid' => 1, 'msg' => '编辑成功!'];
				} else {
					return $res = ['valid' => 0, 'msg' => '密码错误'];
				}
			}
		}
	}
}