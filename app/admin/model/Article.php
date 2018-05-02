<?php
namespace app\admin\model;
use think\Model;
use think\File;

class Article extends Model {
	protected $pk = 'arc_id';
	protected $table = 'blog_arc';
	protected $insert = [ 'sendtime' ];
	protected $update = [ 'updatetime' ];

	protected function setSendTimeAttr ( $value )
	{
		return date('Y-m-d H:i:s', time());
	}

	protected function setUpdateTimeAttr ( $value )
	{
		return date('Y-m-d H:i:s', time());
	}

	public function getres() {
		return $this->where('is_recycle', 1)->order('arc_sort desc, arc_id')->select();
	}

	public function addArc($data, $arc_id) {
		$validate = validate('AddArc');
		if (!$validate->check($data)) {
			return $res = ['valid' => 0, 'msg' => $validate->getError()];
		}
	    $file = request()->file('file');
	    if ($file) {
		    $info = $file->validate(['size'=>15678,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
		    dump($info);
		    if ($info) {
	    		$data['arc_breviary'] =ROOT_PATH . 'public' . DS . 'uploads\\' . $info->getSaveName();
		    } else {
		    	return $res = ['valid' => 0, 'msg' =>  $file->getError()];
		    }
	    }
		if (!isset($data['tag'])) {
			return $res = ['valid' => 0, 'msg' => '请选择标签!'];
		}
		if ($arc_id) {
			$result = $this->allowField( true )->save($data, ['arc_id' => $arc_id]);
		} else {
			$result = $this->allowField( true )->save($data);
		}
		// exit;
		if ( $result ) {
			foreach ( $data[ 'tag' ] as $v ) {
				$arcTagData = [
					'arc_id' => $arc_id || $this->arc_id,
					'tag_id' => $v ,
				];
				$res = db('arc_tag')->insert( $arcTagData );
			}

			if ($res) {
				if ($arc_id) {
					return [ 'valid' => 1 , 'msg' => '编辑成功' ];
				} else {
					return [ 'valid' => 1 , 'msg' => '添加成功' ];
				}
			} else {
				if ($arc_id) {
					return [ 'valid' => 0 , 'msg' => '编辑失败' ];
				} else {
					return [ 'valid' => 0 , 'msg' => '添加失败' ];
				}
			}
		}
		else {
			return [ 'valid' => 0 , 'msg' => $this->getError() ];
		}
	}

	public function oldData($arc_id) {
		return $this->where("arc_id", $arc_id)->find();
	}
	public function arcTag($arc_id) {
		$arcTags = $this->alias('a')
		->join('blog_arc_tag at','a.arc_id = at.arc_id')
		->where('a.arc_id', $arc_id)
		->field('tag_id')
		->select();
		$arcTag = null;
		foreach ($arcTags as $key => $value) {
			$arcTag[$key] = $value['tag_id'];
		}
		return $arcTag;
	}
	public function eidtArc($data) {
		dump($data);
	}
}