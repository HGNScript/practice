<?php
namespace app\index\model;

use think\Model;

class Company extends Model {

	protected $pk = "company_id";
	protected $table = "practice_company";
	protected $insert = [ 'sendtime' ];

	protected function setSendTimeAttr ($value ) {
		return time();
	}

	public function editInfo($data, $company_id) {
		return $this->allowField(true)->save($data, ['company_id' => $company_id]);
	}
	public function addInfo($data) {
		return $this->allowField(true)->save($data);
	}
}