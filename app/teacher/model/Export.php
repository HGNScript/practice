<?php
/**
 * Created by PhpStorm.
 * User: HGN
 * Date: 2018/7/14
 * Time: 20:21
 */

namespace app\teacher\model;


use app\api\controller\v1\Product;
use app\teacher\controller\Csv;
use think\Db;
use think\Model;

class Export extends Model
{
    //导出指定班级学生信息 【需要指定班级ID】
    public function exportMode($class_id)
    {

        $class_name = Db::table('practice_class')->where('class_id', $class_id)->value('class_name');
        $stu_id = Db::table('practice_student')->where('stu_className', $class_name)->column('stu_id');

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $stuData[$key] = (new Stu())->expot($value);
        }
        $fileName = $class_name;


        (new Csv())->down($stuData, $fileName);


    }

    //用导出指定班级学生信息【不许要指定班级ID】
    public function exportJsMode($stu_id)
    {
        ini_set('display_errors', 'Off');
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '1024M');

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $stuData[$key] = (new Stu())->expot($value);
        }



        $fileName = $stuData[0]['stu_className'];


        (new Csv())->down($stuData, $fileName);


    }


    public function export($stuData, $fileName)
    {
        ini_set('display_errors', 'Off');
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '1024M');

        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties();

        $header = [
            '学号',
            '名称',
            '最近一次签到记录',
            '年级',
            '班级名称',
            '教研室',
            '专业',
            '联系电话',
            '身份证号码',
            '班主任名称',
            '班主任联系电话',
            '跟班教师',
            '跟班教师联系电话',
            '实习单位名称',
            '实习单位地址',
            '月薪',
            '实习单位负责人',
            '负责人联系电话',
            '实习岗位'
        ];

        $key = ord("A"); // 设置表头

        foreach ($header as $v) {
            $colum = chr($key);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $key += 1;

        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        foreach ($stuData as $key => $rows) { // 行写入
            $arr = [];
            array_push($arr, $rows['stu_numBer'] . "\t");
            array_push($arr, $rows['stu_name']);
            array_push($arr, $rows['address']);
            array_push($arr, $rows['class_grade']);
            array_push($arr, $rows['stu_className']);
            array_push($arr, $rows['class_staffRoom']);
            array_push($arr, $rows['class_specialty']);
            array_push($arr, $rows['stu_phone'] . "\t");
            array_push($arr, $rows['identity'] . "\t");
            array_push($arr, $rows['classteacher']);
            array_push($arr, $rows['classteacher_phone'] . "\t");
            array_push($arr, $rows['tch_name']);
            array_push($arr, $rows['tch_phone'] . "\t");
            array_push($arr, $rows['company_name']);
            array_push($arr, $rows['company_address']);
            array_push($arr, $rows['company_salary']);
            array_push($arr, $rows['principal']);
            array_push($arr, $rows['principal_phone'] . "\t");
            array_push($arr, $rows['company_position']);




            $span = ord("A");

            foreach ($arr as $keyName => $value) { // 列写入

                $objActSheet->setCellValue(chr($span) . $column, $value);

                $span++;

            }

            $column++;

        }

        unset($stuData);


        $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表


        header('Content-Disposition: attachment;filename=' . $fileName);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output'); // 文件通过浏览器下载

        exit();





    }

    //导出指定班级签到信息
    public function exportSginin($class_id)
    {
        $class_name = Db::table('practice_class')->where('class_id', $class_id)->value('class_name');
        $stu_id = Db::table('practice_student')->where('stu_className', $class_name)->column('stu_id');

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $data = (new Stu())->expotSginin($value);

            $SgininCount = $this->getSgininCount($value);

            array_push($data, sizeof($SgininCount));

            $stuData[$key] = $data;


        }
        $fileName = $class_name.'实习情况统计';

        (new Csv())->downSignin($stuData, $fileName);
    }






    //导出全体学生数据
    public function allExport($tch_id, $flag)
    {
        ini_set('max_execution_time', 0);

        $authority = db('teacher')->where('tch_id', '=', $tch_id)->value('authority');

        if (!$flag) {

            if ($authority == 1) {
                $stu_id = db('student')->order('stu_className')->column('stu_id');
            } else {
                $stu_id = (new Stu())->allTch($tch_id);
            }

            $fileName = '实习信息';

        } else {
            $stu_id = (new Stu())->getNoCompanyStu($tch_id, $authority);
            $fileName = '未填写实习信息学生名单';
        }


        $stuData = array();

        foreach ($stu_id as $key => $value) {
            $stuData[$key] = (new Stu())->expot($value);
        }


        (new Csv())->down($stuData, $fileName);

    }


    //导出全体学生签到信息
    public function allSginExport($tch_id)
    {
        ini_set('max_execution_time', 0);

        $authority = db('teacher')->where('tch_id', '=', $tch_id)->value('authority');

        if ($authority == 1) {
            $stu_id = db('student')->order('stu_className')->column('stu_id');
        } else {
            $stu_id = (new Stu())->allTch($tch_id);
        }

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $data = (new Stu())->expotSginin($value);

            $SgininCount = $this->getSgininCount($value);

            $data['SgininCount'] = sizeof($SgininCount);

            $stuData[$key] = $data;


        }

        $fileName = '实习签到情况统计';

        (new Csv())->downSignin($stuData, $fileName);
    }

    //全体学生日志信息
    public function allSLogsExport($tch_id)
    {
        ini_set('max_execution_time', 0);

        $authority = db('teacher')->where('tch_id', '=', $tch_id)->value('authority');

        if ($authority == 1) {
            $stu_id = db('student')->order('stu_className')->column('stu_id');
        } else {
            $stu_id = (new Stu())->allTch($tch_id);
        }

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $data = (new Stu())->expotSginin($value);

            $logsCount = $this->getLogsCount($value);

            $data['logsCount'] = sizeof($logsCount);

            foreach ($logsCount as $k => $v) {
                array_push($data, $v['replay_val']);

            }

            $stuData[$key] = $data;
        }

        $fileName = '实习反馈情况统计';

        (new Csv())->downLogs($stuData, $fileName);

    }


    //导出指定班级日志信息
    public function exportLogs($class_id){
        $class_name = Db::table('practice_class')->where('class_id', $class_id)->value('class_name');
        $stu_id = Db::table('practice_student')->where('stu_className', $class_name)->column('stu_id');

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $data = (new Stu())->expotSginin($value);

            $logsCount = $this->getLogsCount($value);

            $data['logsCount'] = sizeof($logsCount);

            foreach ($logsCount as $k => $v) {
                array_push($data, $v['replay_val']);

            }


            $stuData[$key] = $data;


        }

        $fileName = $class_name.'实习反馈情况统计';

        (new Csv())->downLogs($stuData, $fileName);
    }



    //获取学生签到数据
    public function getSgininCount($stu_id)
    {
        return Db::table('practice_sginin')
            ->where('stu_id', '=', $stu_id)
            ->select();
    }

    //获取学生日志数据
    public function getLogsCount($stu_id)
    {
        return Db::table('practice_logs')
            ->where('stu_id', '=', $stu_id)
            ->where('replyFlag', '<>', 2)
            ->select();
    }

}

