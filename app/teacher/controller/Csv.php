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
    public function down($stuData, $fileName){
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
            '实习指导教师',
            '实习指导教师联系电话',
            '实习单位(企业)名称',
            '实习地点',
            '月实习补贴',
            '实习单位负责人',
            '负责人联系电话',
            '岗位',
            '是否对口',
            '学生满意度评价',
            '是否学校/老师推荐',
            '提交时间',
        ];

        $csv->put_csv($stuData,$csv_title, $fileName);
    }

    public function downSignin($stuData, $fileName){
        $csv = new csvModel();
        $csv_title = [
            '教研室',
            '专业',
            '班级',
            '姓名',
            '签到次数',
        ];

        $csv->put_csv($stuData,$csv_title, $fileName);
    }

    public function downLogs($stuData, $fileName){
        $csv = new csvModel();
        $csv_title = [
            '教研室',
            '专业',
            '班级',
            '姓名',
            '反馈次数',
            '第一次',
            '第二次',
            '第三次',
            '第四次',
            '第五次',
            '第六次',
            '第七次',
            '第八次',
            '教师评分'

        ];

        $csv->put_csv($stuData,$csv_title, $fileName);
    }



}