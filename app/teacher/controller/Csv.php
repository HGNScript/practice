<?php
/**
 * Created by PhpStorm.
 * User: HGN
 * Date: 2018/7/28
 * Time: 14:08
 */

namespace app\teacher\controller;

use app\teacher\model\Csv as csvModel;
use think\Db;

class Csv
{
    public function down($stuData){
        $csv = new csvModel();
//        $list = Db::name('users')->field("id,phone,username,nickname,is_vip,end_time,level")->select();
        $csv_title = [
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

        $csv->put_csv($stuData,$csv_title);
    }


}