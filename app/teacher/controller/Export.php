<?php
namespace app\teacher\controller;

use app\teacher\model\Stu;
use think\Session;

class Export extends Common
{
    protected $cla;
    protected $stu;

    protected function _initialize()
    {
        parent::_initialize();
        $this->cla = new \app\teacher\model\Cla();
        $this->stu = new \app\teacher\model\Stu();
        $this->tch = new \app\teacher\model\Index();
    }

    public function index()
    {
        $tch_id = session('tch.tch_id');
        $authority = $this->tch->getAuthority($tch_id);


        if ($authority == 1) {
            // $classinfo = db('class')->select();

            $grade = db('class')->order('class_grade desc')->column('class_grade');
            //获取年级(过滤重复的年级)
            foreach ($grade as $key => $v) {
                $gradeArr = $grade;
                unset($gradeArr[$key]);
                if (in_array($grade[$key], $gradeArr)) {
                    unset($grade[$key]);
                }
            }
            $specialty = db('class')->column('class_specialty');
            foreach ($specialty as $key => $v) {
                $specialtyArr = $specialty;
                unset($specialtyArr[$key]);
                if (in_array($specialty[$key], $specialtyArr)) {
                    unset($specialty[$key]);
                }
            }

            $this->assign('grade', $grade);
            $this->assign('specialty', $specialty);

        } else {

            $classinfo = db('class')->where('tch_id', $tch_id)->select();
            foreach ($classinfo as $key => $value) {
                $sum = db('student')->where('stu_className', $value['class_name'])->select();
                $classinfo[$key]['class_sum'] = sizeof($sum);
            }
            $this->assign('classinfo', $classinfo);

        }


        if (request()->isAjax()) {
            $curr = input('get.curr');
            $limit = input('get.limit');
            $search = input('post.search');
            $select = input('post.');

            if ($select) {
                if ($search) {
                    $classinfo = db('class')->where("class_grade like '%$search%' OR class_name like '%$search%' OR class_staffRoom like '%$search%' OR class_specialty like '%$search%'")->order('class_grade desc')->select();
                } else {
                    $specialty = $select[2];

                    if ($select[1]) {
                        $specialtys = db('class')->where('class_staffRoom', $select[1])->column('class_specialty');
                    } else {
                        $specialtys = db('class')->column('class_specialty');
                    }

                    if (!in_array($specialty, $specialtys)) {
                        $select[2] = '';
                    }

                    $classinfo = $this->cla->exportClass($select);
                }

            } else {
                $classinfo = db('class')->order('class_grade desc')->select();
            }


            if ($curr) {
                $star = ($curr - 1) * $limit;
                $classinfo = array_slice($classinfo, $star, $limit);
                foreach ($classinfo as $key => $value) {
                    $sum = db('student')->where('stu_className', $value['class_name'])->select();
                    $classinfo[$key]['class_sum'] = sizeof($sum);
                }


                if ($select) {
                    if (!$search) {
                        foreach ($specialtys as $key => $v) {
                            $specialtysArr = $specialtys;
                            unset($specialtysArr[$key]);
                            if (in_array($specialtys[$key], $specialtysArr)) {
                                unset($specialtys[$key]);
                            }
                        }

                        $res = ["data" => $classinfo, "specialtys" => $specialtys, "specialty" => $specialty];
                        return json($res);
                    }
                }

                return json($classinfo);
            } else {
                return sizeof($classinfo);
            }
        }

        $this->assign('authority', $authority);
        return $this->fetch();
    }

    public function excel()
    {
        $class_id = input('get.class_id');
        $stu_id = input('post.');

        if ($class_id) {
            (new \app\teacher\model\Export())->exportMode($class_id);
        } else {
            (new \app\teacher\model\Export())->exportJsMode($stu_id);
        }



    }

    public function exportCount(){
        $class_id = input('get.class_id');
        return (new \app\teacher\model\Export())->exportCountMode($class_id);
    }


    public function allExport(){
        $tch_id = Session::get('tch');

         (new \app\teacher\model\Export())->allExport($tch_id['tch_id']);
    }

    public function allCountExport(){
        $tch_id = Session::get('tch');

        (new \app\teacher\model\Export())->allCountExport($tch_id['tch_id']);
    }

}