<?php
namespace app\index\model;
use think\Model;
use think\Dd;

class Logs extends Model{
	protected $pk = "logs_id";
	protected $table = "practice_logs";
	protected $insert = [ 'sendtime' ];

	protected function setSendTimeAttr ($value ) {
		return time();
	}

	public function oldlogs($logs_id) {
		return $this->where('logs_id', $logs_id)->find();
	}
	public function Logs($stu_id) {
		return $this->where('stu_id', $stu_id)->order('sendtime desc')->select();
	}

	public function addLogs($data,$logs_id) {
	    if ($logs_id && $logs_id != 'undefined'){
            $data['logs_reply'] = '';
            $data['replyFlag'] = 0;
            $data['replay_val'] = '';
            $data['redadFlag'] = 0;

            return $this->save($data,['logs_id' => $logs_id]);
        } else {
            return $this->save($data);
        }
	}

	public function notice($stu_id) {
//		return $this
//            ->where('stu_id', $stu_id)
//		    ->where('replyFlag', '=', 1)
//            ->whereOr('replyFlag', '=', 2)
//		    ->where('readFlag', 0)
//		    ->select();

		return $this->where("stu_id = '$stu_id' AND replyFlag = 1 AND readFlag = 0 OR stu_id = '$stu_id' AND replyFlag = 2 AND readFlag = 0  ")
            ->select();
	}

	public function getLogs() {
		return $this->order('sendtime desc')->limit(1)->select();
	}
}