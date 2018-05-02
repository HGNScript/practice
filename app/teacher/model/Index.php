<?php
namespace app\teacher\model;

use think\Model;

class Index extends Model {

	protected $pk = "tch_id";
	protected $table = "practice_teacher";

	public function getAuthority($tch_id){
		return $this->where('tch_id', $tch_id)->value('authority');
	}
}