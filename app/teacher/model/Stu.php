<?php
namespace app\teacher\model;

use app\api\controller\v1\Product;
use think\Model;

class Stu extends Model
{

    protected $pk = "stu_id";
    protected $table = "practice_student";

    /**
     * 获取学生数据
     */
    public function getStu($stu_id)
    {
        return $this->alias('s')
            ->join('practice_company c', ' s.stu_id = c.stu_id', 'LEFT')
            ->join('practice_class l', ' s.stu_className = l.class_name', 'LEFT')
            ->join('practice_teacher t', ' l.tch_id = t.tch_id', 'LEFT')
            ->where('s.stu_id', $stu_id)
            ->order('c.sendtime desc')
            ->limit(1)
            ->select();
    }

    public function getStuInfo($stu_id)
    {
        return $this->alias('s')
            ->join('practice_company c', ' s.stu_id = c.stu_id', 'LEFT')
            ->where('s.stu_id', $stu_id)
            ->order('c.sendtime desc')
            ->limit(1)
            ->find();
    }

    /**
     * 指定班级学生模糊搜索
     */
    public function sreach($data, $class_id)
    {
        return $this->alias('s')
            ->join('practice_company c', ' s.stu_id = c.stu_id', 'LEFT')
            ->where("s.class_id= '$class_id' AND s.stu_numBer like '%$data%' OR s.class_id='$class_id' AND s.stu_name like '%$data%' OR s.class_id='$class_id' AND c.company_name like '%$data%' OR s.class_id='$class_id' AND c.company_address like '%$data%' ")
            ->column('s.stu_id');
    }

    public function addStu($data)
    {
        return $this->save($data);
    }

    public function edit($data, $stu_id)
    {
        return $this->save($data, ['stu_id' => $stu_id]);
    }

    public function getSignin($stu_id)
    {
        return $this->alias('s')
            ->join('practice_sginin i', 's.stu_id = i.stu_id', 'LEFT')
            ->where('s.stu_id', $stu_id)
            ->order('i.sendtime desc')
            ->limit(1)
            ->find();
    }

    public function getLogs($class_name, $changge)
    {
        if ($changge) {
            return $this->alias('s')
                ->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
                ->where('s.stu_className', $class_name)
                ->where('l.replyFlag', "<>", 2)
                ->order('l.replyFlag, sendtime desc')
                ->select();
        } else {
            return $this->alias('s')
                ->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
                ->where('s.stu_className', $class_name)
                ->where('l.replyFlag', "<>", 2)
                ->order('l.replyFlag desc, sendtime desc')
                ->select();
        }
    }

    public function getUnLogstStu($select, $search, $class_name)
    {
        $logstStuID = $this->getLogstStuID($select, $class_name);

        if ($search) {
            $stu_id = $this->where('stu_className', '=', $class_name)->where("stu_numBer like '%$search%' OR stu_name like '%$search%'")
                ->order('stu_numBer')
                ->column('stu_id');

        } else {
            $stu_id = $this->where('stu_className', '=', $class_name)->order('stu_numBer')->column('stu_id');

        }


        foreach ($stu_id as $key => $value) {
            if (in_array($value, $logstStuID)) {
                unset($stu_id[$key], $value);
            }
        }
        return $stu_id;
    }

    //获取指定月份已填写日志的学生ID
    public function getLogstStuID($select, $class_name)
    {
        $stu_id = $this->where('stu_className', '=', $class_name)->column('stu_id');

        $logsIDArr = [];

        foreach ($stu_id as $key => $value) {
            $logs = db('logs')
                ->where('stu_id', $value)
                ->where('replyFlag', '<>', 2)
                ->select();

            foreach ($logs as $k => $v) {
                if (date('m', $v['sendtime']) == $select) {
                    array_push($logsIDArr, $v['stu_id']);
                }
            }

        }


        return $logsIDArr;
    }


    public function getunLogs($stu_id, $select)
    {
        if ($select) {
            return $this->alias('s')
                ->join('practice_logs l', 's.stu_id = l.stu_id', 'left')
                ->where('s.stu_id', $stu_id)
                ->where('l.replyFlag', '<>', 2)
                ->order('l.sendtime desc')
                ->limit(1)
                ->select();

        } else {
            return $this->alias('s')
                ->join('practice_logs l', 's.stu_id = l.stu_id', 'left')
                ->where('s.stu_id', $stu_id)
                ->where('l.replyFlag', '<>', 2)
                ->where('s.logsFlag', 0)
                ->order('l.sendtime desc')
                ->limit(1)
                ->select();
        }

    }

    public function search($class_name, $search, $changge)
    {
        if ($changge) {
            return $this->alias('s')
                ->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
                ->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
                ->order('s.stu_numBer')
                ->select();
        } else {
            return $this->alias('s')
                ->join('practice_logs l', 's.stu_id = l.stu_id', 'RIGHT')
                ->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
                ->order('s.stu_numBer')
                ->select();
        }
    }

    public function searchLogs($class_name, $search)
    {
        return $this->where("logsFlag= 0 AND stu_className= '$class_name' AND stu_numBer like '%$search%' OR logsFlag= 0 AND stu_className='$class_name' AND stu_name like '%$search%'")
            ->order('stu_numBer')
            ->column('stu_id');
    }

    public function searchsign($class_name, $search, $changge)
    {
        if ($changge) {
            return $this->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
                ->where('signInFlag', 0)
                ->order('stu_numBer')
                ->column('stu_id');
        } else {
            return $this->where("stu_className= '$class_name' AND stu_numBer like '%$search%' OR stu_className='$class_name' AND stu_name like '%$search%'")
                ->where('signInFlag', 0)
                ->order('stu_numBer')
                ->column('stu_id');
        }
    }

    public function expot($stu_id)
    {
        return $this->alias('s')
            ->join('practice_company c', 's.stu_id = c.stu_id', 'left')
            ->join('practice_class l', 's.stu_className = l.class_name', 'left')
            ->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
            ->join('practice_sginin g', 's.stu_id = g.stu_id', 'left')
            ->where('s.stu_id', $stu_id)
            ->order('g.sendtime desc, c.sendtime desc')
            ->limit(1)
            ->field('s.stu_numBer,s.stu_name,g.address,l.class_grade,s.stu_className,l.class_staffRoom,l.class_specialty,s.stu_phone,s.identity,s.classteacher,s.classteacher_phone,t.tch_name,t.tch_phone,c.company_name,c.company_address,c.company_salary,c.principal,c.principal_phone,c.company_position')
            ->find()->toArray();

    }

    public function company($stu_id)
    {
        return $this->alias('s')
            ->join('practice_company c', ' s.stu_id = c.stu_id', 'LEFT')
            ->where('s.stu_id', $stu_id)
            ->order('c.sendtime desc')
            ->select();
    }

    public function OnCompany($stu_id)
    {
        return $this->alias('s')
            ->join('practice_company c', ' s.stu_id = c.stu_id', 'LEFT')
            ->where('s.stu_id', $stu_id)
            ->order('c.sendtime')
            ->find();
    }

    public function sreachScore($data, $class_name)
    {
        return $this->where("stu_className= '$class_name' AND stu_numBer like '%$data%' OR stu_className='$class_name' AND stu_name like '%$data%'")
            ->order('stu_numBer')
            ->select();
    }

//    获取指定周的签到信息，已签或未签
    public function getWeekSign($select, $class_name, $search, $flag)
    {
        $signStuID = $this->getSignStuID($select, $class_name);

        if ($select == date('W', time())) {
            foreach ($signStuID as $key => $value) {
                $this->where('stu_id', '=', $value)
                    ->update(['signInFlag' => 1]);
            }
        }

        if (!$flag) {
            if ($search) {
                $stu_id = $this->where('stu_className', '=', $class_name)->where("stu_numBer like '%$search%' OR stu_name like '%$search%'")
                    ->order('stu_numBer')
                    ->column('stu_id');

            } else {
                $stu_id = $this->where('stu_className', '=', $class_name)->order('stu_numBer')->column('stu_id');

            }
        } else {

            if ($search) {
                $data = [];
                $stu_id = $this->where('stu_className', '=', $class_name)->where("stu_numBer like '%$search%' OR stu_name like '%$search%'")
                    ->order('stu_numBer')
                    ->column('stu_id');

                foreach ($stu_id as $key => $value) {

                    if (in_array($value, $signStuID)) {

                        array_push($data, $value);
                    }
                }
                return $data;
            } else {
                $stu_id = $this->where('stu_className', '=', $class_name)->order('stu_numBer')->column('stu_id');
            }

        }


        foreach ($stu_id as $key => $value) {
            if (in_array($value, $signStuID)) {
                unset($stu_id[$key], $value);
            }
        }

        if (!$flag) {
            return $stu_id;
        } else {
            return $signStuID;
        }


    }

//    获取指定时间有签到信息的学生ID
    public function getSignStuID($select, $class_name)
    {
        $stu_id = $this->where('stu_className', '=', $class_name)->column('stu_id');

        $signIDArr = [];

        foreach ($stu_id as $key => $value) {

            $sign = db('sginin')
                ->where('stu_id', $value)
                ->select();

            foreach ($sign as $k => $v) {
                if (date('W', $v['sendtime']) == $select) {
                    if ($v) {
                        array_push($signIDArr, $v['stu_id']);
                    }
                }
            }
        }

        foreach ($signIDArr as $key => $v) {
            $arr = $signIDArr;
            unset($arr[$key]);
            if (in_array($signIDArr[$key], $arr)) {
                unset($signIDArr[$key]);
            }
        }

        return $signIDArr;
    }

    public function getAllStuInfo($tch_id, $search, $authority)
    {
        if ($authority == 1) {
            if (!$search) {
                $stu_id = $this->alias('s')
                    ->join('practice_class l', 'l.class_id = s.class_id', 'left')
                    ->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
                    ->order('s.stu_numBer')
                    ->column('s.stu_id');

            } else {
                $stu_id = $this->alias('s')
                    ->join('practice_company c', ' s.stu_id = c.stu_id', 'LEFT')
                    ->join('practice_class l', 'l.class_id = s.class_id', 'left')
                    ->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
                    ->where("s.stu_numBer like '%$search%' OR s.stu_name like '%$search%' OR c.company_name like '%$search%' OR c.company_address like '%$search%' ")
                    ->order('s.stu_numBer')
                    ->column('s.stu_id');
            }
        } else {
            if (!$search) {
                $stu_id = $this->alias('s')
                    ->join('practice_class l', 'l.class_id = s.class_id', 'left')
                    ->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
                    ->where('t.tch_id', $tch_id)
                    ->order('s.stu_numBer')
                    ->column('s.stu_id');

            } else {
                $stu_id = $this->alias('s')
                    ->join('practice_company c', ' s.stu_id = c.stu_id', 'LEFT')
                    ->join('practice_class l', 'l.class_id = s.class_id', 'left')
                    ->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
                    ->where("t.tch_id= '$tch_id' AND s.stu_numBer like '%$search%' OR t.tch_id= '$tch_id' AND s.stu_name like '%$search%' OR t.tch_id= '$tch_id' AND c.company_name like '%$search%' OR t.tch_id= '$tch_id' AND c.company_address like '%$search%' ")
                    ->order('s.stu_numBer')
                    ->column('s.stu_id');
            }
        }


        foreach ($stu_id as $key => $v) {
            $arr = $stu_id;
            unset($arr[$key]);
            if (in_array($stu_id[$key], $arr)) {
                unset($stu_id[$key]);
            }
        }


        $stuData = [];
        foreach ($stu_id as $key => $value) {
            $data = $this->getStuInfo($value);

            array_push($stuData, $data);
        }


        foreach ($stuData as $key => $value) {
            $value['signInFlag'] ? $value['signInFlag'] = '<span class="layui-badge layui-bg-green">是</span>' : $value['signInFlag'] = '<span class="layui-badge">否</span>';
            $value['logsFlag'] ? $value['logsFlag'] = '<span class="layui-badge layui-bg-green">是</span>' : $value['logsFlag'] = '<span class="layui-badge">否</span>';
            if (!$value['company_name']) {
                $value['company_name'] = '<span class="layui-badge">没有实习信息</span>';
            }

            if (!$value['company_address']) {
                $value['company_address'] = '<span class="layui-badge">没有实习信息</span>';
            }

            $stu_id = db('student')->where('stu_numBer', $value['stu_numBer'])->value('stu_id');
            $stuData[$key]['stu_id'] = $stu_id;
        }

        return $stuData;
    }


    public function allTch($tch_id)
    {
        return $this->alias('s')
            ->join('practice_class l', 'l.class_id = s.class_id', 'left')
            ->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
            ->where('t.tch_id', '=', $tch_id)
            ->order('s.stu_numBer')
            ->column('s.stu_id');

    }

    public function getNoCompanyStu($tch_id, $authority)
    {

        if ($authority == 1) {

        } else {
            $stuIDAllY = $this->alias('s')
                ->join('practice_class l', 'l.class_id = s.class_id', 'left')
                ->join('practice_teacher t', 't.tch_id = l.tch_id', 'left')
                ->join('practice_company c', 'c.stu_id = s.stu_id', 'right')
                ->where('t.tch_id', '=', $tch_id)
                ->order('s.stu_numBer')
                ->column('s.stu_id');

            $stuIDAll = $this->allTch($tch_id);
        }


        foreach ($stuIDAll as $key => &$v) {

            if (in_array($v, $stuIDAllY)) {
                unset($stuIDAll[$key]);
            }

        }

        return $stuIDAll;

    }


}
