<?php
/**
 * Created by PhpStorm.
 * User: HGN
 * Date: 2018/7/14
 * Time: 20:21
 */

namespace app\teacher\model;


use think\Db;
use think\Model;

class Export extends Model
{
    public function exportMode($class_id)
    {

        $class_name = Db::table('practice_class')->where('class_id', $class_id)->value('class_name');
        $stu_id = Db::table('practice_student')->where('stu_className', $class_name)->column('stu_id');

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $stuData[$key] = (new Stu())->expot($value);
        }
        $fileName = $class_name . '.xls';


        $this->export($stuData, $fileName);


    }

    public function exportJsMode($stu_id)
    {
        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $stuData[$key] = (new Stu())->expot($value);
        }

        $fileName = $stuData[0]['stu_className'] . '.xls';

        $this->export($stuData, $fileName);
    }


    private function export($stuData, $fileName)
    {


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


        $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表


        header('Content-Disposition: attachment;filename=' . $fileName);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output'); // 文件通过浏览器下载

        exit;
    }


    public function exportCountMode($class_id)
    {
        $class_name = Db::table('practice_class')->where('class_id', $class_id)->value('class_name');
        $stu_id = Db::table('practice_student')->where('stu_className', $class_name)->column('stu_id');

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $data = (new Stu())->expot($value);

            $SgininCount = $this->getSgininCount($value);
            $LogsCount = $this->getLogsCount($value);

            $data['SgininCount'] = sizeof($SgininCount);
            $data['LogsCount'] = sizeof($LogsCount);

            $stuData[$key] = $data;


        }
        $fileName = $class_name . '.xls';


        $this->exportCount($stuData, $fileName);
    }

    private function exportCount($stuData, $fileName)
    {


        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties();

        $header = [
            '学号',
            '名称',
            '班级名称',
            '联系电话',
            '签到记录数',
            '实习月记录数',
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
            array_push($arr, $rows['stu_className']);
            array_push($arr, $rows['stu_phone'] . "\t");
            array_push($arr, $rows['SgininCount'] . "\t");
            array_push($arr, $rows['LogsCount'] . "\t");


            $span = ord("A");

            foreach ($arr as $keyName => $value) { // 列写入

                $objActSheet->setCellValue(chr($span) . $column, $value);

                $span++;

            }

            $column++;

        }


        $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表


        header('Content-Disposition: attachment;filename=' . $fileName);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output'); // 文件通过浏览器下载

        exit;
    }

    public function getSgininCount($stu_id)
    {
        return Db::table('practice_sginin')
            ->where('stu_id', '=', $stu_id)
            ->select();
    }

    public function getLogsCount($stu_id)
    {
        return Db::table('practice_logs')
            ->where('stu_id', '=', $stu_id)
            ->where('replyFlag', '<>', 2)
            ->select();
    }

    public function allExport($tch_id, $flag)
    {

        if (!$flag) {
            $stu_id = (new Stu())->allTch($tch_id);
            $fileName = '全体数据' . '.xls';

        } else {
            $stu_id = (new Stu())->getNoCompanyStu($tch_id);
            $fileName = '全体数据-未填写实习信息' . '.xls';
        }


        $stuData = array();

        foreach ($stu_id as $key => $value) {
            $stuData[$key] = (new Stu())->expot($value);
        }


        $this->export($stuData, $fileName);
    }


    public function allCountExport($tch_id)
    {


        $stu_id = (new Stu())->allTch($tch_id);

        $stuData = array();
        foreach ($stu_id as $key => $value) {
            $data = (new Stu())->expot($value);

            $SgininCount = $this->getSgininCount($value);
            $LogsCount = $this->getLogsCount($value);

            $data['SgininCount'] = sizeof($SgininCount);
            $data['LogsCount'] = sizeof($LogsCount);

            $stuData[$key] = $data;


        }

        $fileName = '全体数据' . '.xls';

        $this->exportCount($stuData, $fileName);
    }


}

