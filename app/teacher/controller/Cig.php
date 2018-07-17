<?php
/**
 * Created by PhpStorm.
 * User: HGN
 * Date: 2018/7/16
 * Time: 23:25
 */

namespace app\teacher\controller;


use think\Controller;

class Cig extends Controller
{
    public function index(){

        $week = db('config')->where('config_key', '=', 'week')->value('config_val');

        $week = date("Y-m-d ", $week);

        $this->assign('week', $week);
        return $this->fetch('index');
    }

    public function editWeek(){
        $week = input('post.week');

        $week = strtotime($week);


        $res = db('config')->where('config_key', '=', 'week')->update(['config_val' => $week]);

        if ($res) {
            return json(['valid' => 1, 'msg' => '设置成功']);
        } else {
            return json(['valid' => 0, 'msg' => '设置失败']);
        }
    }
}