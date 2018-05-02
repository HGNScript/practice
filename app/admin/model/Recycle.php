<?php
namespace app\admin\model;
use think\Model;

class Recycle extends Model {
	protected $pk = 'arc_id';
	protected $table = 'blog_arc';
	public function getres() {
		return $this->where('is_recycle', 0)->order('arc_sort desc, arc_id')->select();
	}
}