<?php
namespace app\teacher\controller;

use app\index\model\Company;
use app\teacher\model\Config;
use app\teacher\model\Stu;

class Checkc extends Common
{
    protected $tch;
    protected $stu;
    protected $cla;

    protected function _initialize()
    {
        parent::_initialize();
        $this->tch = new \app\teacher\model\Index();
        $this->stu = new \app\teacher\model\Stu();
        $this->cla = new \app\teacher\model\Cla();
    }

    /**
     * 班级信息查看
     */
    public function index()
    {
        $id = session('tch.tch_id');
        $authority = $this->tch->getAuthority($id);
        $tch_id = input('param.tch_id');
        $class_id = input('param.class_id');
        $class_name = db('class')->where('class_id', $class_id)->value('class_name');
        $sum = db('student')->where('class_id', $class_id)->select();
        $stu_id = db('student')->where('class_id', $class_id)->order('signinFlag, logsFlag')->column('stu_id');

        if (request()->isAjax()) {
            $class_id = input('post.class_id');
            $stu_id = db('student')->where('class_id', $class_id)->column('stu_id');
            $stuData = array();
            foreach ($stu_id as $key => $value) {
                $stuData[$key] = $this->stu->getStuInfo($value);
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

            return json($stuData);
        }

        $this->assign('tch_id', $tch_id);
        $this->assign('sum', sizeof($sum));
        $this->assign('class_name', $class_name);
        $this->assign('class_id', $class_id);
        $this->assign('authority', $authority);
        return $this->fetch();
    }

    /**
     * 搜索数据分页长度
     */
    public function indexLen()
    {
        $data = input('post.info');
        $class_id = input('post.class_id');
        $stu_id = $this->stu->sreach($data, $class_id);

        foreach ($stu_id as $key => $v) {
            $arr = $stu_id;
            unset($arr[$key]);
            if (in_array($stu_id[$key], $arr)) {
                unset($stu_id[$key]);
            }
        }
        $stuData = [];
        foreach ($stu_id as $key => $value) {
            $data = $this->stu->getStuInfo($value);

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


        return json($stuData);

    }


    /**
     * 学生信息详情
     */
    public function stuinfo()
    {
        $stu_id = input('get.id');
        $flag = input('get.flag');
        $data = $this->stu->getStu($stu_id);
        $sginin = db('sginin')->where('stu_id', $stu_id)->limit(4)->order('sendtime desc')->select();
        $logs = db('logs')->where('stu_id', $stu_id)->limit(4)->order('sendtime desc')->select();

        $id = session('tch.tch_id');
        $authority = $this->tch->getAuthority($id);

        foreach ($logs as $key => $value) {
            $str = $logs[$key]['logs_content'];
            $str = mb_substr(strip_tags($str), 0, 15, 'utf-8');
            $logs[$key]['logs_content'] = $str;
        }
        $this->assign('sginin', $sginin);
        $this->assign('logs', $logs);
        $this->assign('data', $data[0]);
        $this->assign('flag', $flag);
        $this->assign('stu_id', $stu_id);
        $this->assign('authority', $authority);

        return $this->fetch();
    }


    public function editCompanyInfo()
    {

        $data = input('post.');

        $company = db('company')
            ->where('stu_id', $data['stu_id'])
            ->where('company_id', $data['company_id'])
            ->select();

        if ($company) {
            $res = db('company')
                ->where('stu_id', $data['stu_id'])
                ->where('company_id', $data['company_id'])
                ->update([$data['name'] => $data['val']]);
        } else {
            $res = Company::create([$data['name'] => $data['val'], 'stu_id' => $data['stu_id']]);
        }

        if ($res) {
            return json([
                'res' => $res,
                'valid' => 1,
                'msg' => '编辑成功',
            ]);
        } else {
            return json([
                'res' => $res,
                'valid' => 0,
                'msg' => '编辑失败',
            ]);
        }


    }

    public function editStuInfo()
    {

        $data = input('post.');

        $res = db('student')
            ->where('stu_id', $data['stu_id'])
            ->update([$data['name'] => $data['val']]);

        if ($res) {
            return json([
                'res' => $res,
                'valid' => 1,
                'msg' => '编辑成功',
            ]);
        } else {
            return json([
                'res' => $res,
                'valid' => 0,
                'msg' => '编辑失败',
            ]);
        }


    }


    public function company()
    {
        $stu_id = input('get.stu_id');
        $data = $this->stu->company($stu_id);
        if (request()->isAjax()) {
            $curr = input('post.curr');
            $limit = input('post.limit');
            $stu_id = input('get.stu_id');

            $data = $this->stu->company($stu_id);
            foreach ($data as $key => $value) {
                $data[$key]['sendtime'] = date('Y-m-d', $data[$key]['sendtime']);
            }

            if ($curr) {
                $star = ($curr - 1) * $limit;
                $data = array_slice($data, $star, $limit);
                return json($data);
            } else {
                return sizeof($data);
            }
        }

        $this->assign('len', sizeof($data));
        $this->assign('stu_id', $stu_id);
        return $this->fetch();
    }

    public function signin()
    {
        $stu_id = input('get.stu_id');
        $signin = db('sginin')->where('stu_id', $stu_id)->order('sendtime desc')->select();



        if (request()->isAjax()) {
            $stu_id = input('get.stu_id');
            $curr = input('post.curr');
            $limit = input('post.limit');
            $star = ($curr - 1) * $limit;
            $data = db('sginin')->where('stu_id', $stu_id)->order('sendtime desc')->select();
            $data = array_slice($data, $star, $limit);
            foreach ($data as $key => $value) {
                $data[$key]['sendtime'] = date('Y-m-d h:i:s', $data[$key]['sendtime']);
            }
            return json($data);
        }
        $this->assign('len', sizeof($signin));
        $this->assign('stu_id', $stu_id);

        return $this->fetch();
    }

    public function logs()
    {
        $stu_id = input('get.stu_id');
        $logs = db('logs')->where('stu_id', $stu_id)->order('sendtime desc')->select();
        if (request()->isAjax()) {
            $stu_id = input('get.stu_id');
            $curr = input('post.curr');
            $limit = input('post.limit');
            $star = ($curr - 1) * $limit;
            $data = db('logs')->where('stu_id', $stu_id)->order('sendtime desc')->select();
            $data = array_slice($data, $star, $limit);

            foreach ($data as $key => $value) {
                $data[$key]['sendtime'] = date('Y-m-d h:i:s', $data[$key]['sendtime']);
            }
            foreach ($data as $key => $value) {
                $str = $data[$key]['logs_content'];
                $str = mb_substr(strip_tags($str), 0, 15, 'utf-8');
                $data[$key]['logs_content'] = $str;
            }
            return json($data);
        }
        $this->assign('len', sizeof($logs));
        $this->assign('stu_id', $stu_id);
        return $this->fetch();
    }

    /**
     * 班级数据统计
     */
    public function proportion()
    {
        $class_name = input('post.class_name');
        $sum = db('student')->where('stu_className', $class_name)->select();
        $signin = db('student')->where('stu_className', $class_name)->where('signInFlag', 1)->select();
        $unSignin = sizeof($sum) - sizeof($signin);
        $data = array('signin' => sizeof($signin), 'unSignin' => $unSignin);

        $logs = db('student')->where('stu_className', $class_name)->where('logsFlag', 1)->select();
        $unLogs = sizeof($sum) - sizeof($logs);
        $data['logs'] = sizeof($logs);
        $data['unLogs'] = $unLogs;

        return json($data);
    }

    public function del()
    {
        $stu_id = input('post.');
        $res = db('student')->delete($stu_id);

        foreach ($stu_id as $key => $value) {
            $logs = db('logs')->where('stu_id', $value)->delete();
            $sign = db('sginin')->where('stu_id', $value)->delete();
            $company = db('company')->where('stu_id', $value)->delete();
        }

        if ($res) {
            return json($res = ['valid' => 1, 'msg' => '删除成功']);
        }
    }

    /**
     * 导入数据过滤, 过滤工号重复的数据
     * @param  [type] $data excel表格数据
     */
    public function arrOnly($data)
    {
        $datao = [];
        foreach ($data as $key => $value) {
            $datao[$key] = $value['stu_numBer'];
        }
        foreach ($datao as $key => $v) {
            $arr = $datao;
            unset($arr[$key]);
            if (in_array($datao[$key], $arr)) {
                unset($datao[$key]);
            }
        }
        $claInfo = [];
        foreach ($data as $key => $value) {
            foreach ($datao as $k => $va) {
                if ($key == $k) {
                    array_push($claInfo, $value);
                }
            }
        }
        return $claInfo;
    }


    /**
     * 接收提交的excl表格文件,获取excel 表格数据,将数据转化为数组
     * (提交的教师数据,工号不能重复)
     */
    public function excel()
    {
        // import('phpexcel.PHPExcel', EXTEND_PATH);//方法二
        $class_id = input('get.class_id');
        $class_name = db('class')->where('class_id', $class_id)->value('class_name');
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $file = request()->file('excel');
        $info = $file->validate(['size' => 80000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');
        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            $data = [];
            $len = sizeof($excel_array);
            // dump($excel_array);
            foreach ($excel_array as $k => $v) {
                $n = db('student')->where('stu_numBer', $v[1])->select();
                if (!$n) {
                    $data[$k]['stu_name'] = $v[0];
                    $data[$k]['stu_numBer'] = $v[1];
                    $data[$k]['stu_phone'] = $v[2];
                    $data[$k]['identity'] = $v[3];
                    $data[$k]['stu_className'] = $class_name;
                    $data[$k]['stu_password'] = md5('gzcj');
                    $data[$k]['classteacher'] = $v[4];
                    $data[$k]['classteacher_phone'] = $v[5];
                    $data[$k]['class_id'] = $class_id;
                }
            }

            $data = $this->arrOnly($data);
            $success = db('student')->insertAll($data);
            $error = $len - $success;
            if ($success) {
                return json($res = ['valid' => 1, 'msg' => "共导入{$len}条，成功{$success}条，失败{$error}条。"]);
            } else {
                return json($res = ['valid' => 0, 'msg' => "共导入{$len}条，成功0条，失败{$error}条。"]);
            }
        } else {
            return json($res = ['valid' => 0, 'msg' => $file->getError()]);
        }
    }

    public function addstu()
    {
        $class_id = input('get.class_id');

        if (request()->isAjax()) {
            $class_id = input('get.class_id');
            $class_name = $class_name = db('class')->where('class_id', $class_id)->value('class_name');
            $data = input('post.');
            $validate = validate('AddStu');
            if (!$validate->check($data)) {
                $res = ['valid' => 0, 'msg' => $validate->getError()];
            } else {
                $number = db('student')->where('stu_numBer', $data['stu_numBer'])->find();
                if (!$number) {
                    $data['stu_password'] = md5('gzcj');
                    $data['stu_className'] = $class_name;
                    $data['class_id'] = $class_id;
                    $info = $this->stu->addStu($data);
                    if ($info) {
                        $res = ['valid' => 1, 'msg' => '添加成功!'];
                    } else {
                        $res = ['valid' => 0, 'msg' => '添加失败!'];
                    }
                } else {
                    $res = ['valid' => 0, 'msg' => '学号不能重复!'];
                }
            }
            if ($res['valid']) {
                return json($res);
            } else {
                return json($res);
            }
        }
        $this->assign('class_id', $class_id);
        return $this->fetch();
    }

    public function edit()
    {
        $stu_id = input('get.stu_id');
        $oldData = db('student')->where('stu_id', $stu_id)->find();
        $this->assign('oldData', $oldData);

        $id = session('tch.tch_id');
        $authority = $this->tch->getAuthority($id);

        if (request()->isAjax()) {
            $data = input('post.');
            $validate = validate('AddStu');
            if (!$validate->check($data)) {
                $res = ['valid' => 0, 'msg' => $validate->getError()];
            } else {
                $stu_id = $data['stu_id'];
                $stu_numBer = $data['stu_numBer'];
                $n = db('student')->where("stu_id<>'$stu_id' AND stu_numBer='$stu_numBer' ")->select();
                if ($n) {
                    $res = ['valid' => 0, 'msg' => '学号不能重复!'];
                } else {
                    unset($data['stu_id']);
                    $info = $this->stu->edit($data, $stu_id);
                    if ($info) {
                        $res = ['valid' => 1, 'msg' => '编辑成功!'];
                    } else {
                        $res = ['valid' => 0, 'msg' => '编辑失败!'];
                    }
                }
            }
            return json($res);
        }

        $this->assign('authority', $authority);
        return $this->fetch();
    }


    public function signininfo()
    {
        $class_id = input('get.class_id');
        if (request()->isAjax()) {
            $class_id = input('get.class_id');
            $changge = input('get.changge');
            $chang = $changge;
            $search = input('post.sea');
            $select = input('post.select');

            $class_name = db('class')->where('class_id', $class_id)->value('class_name');

            if (!$select) {
                if ($search) {
                    $stu_id = $this->stu->searchsign($class_name, $search, $chang);
                } else {
                    $stu_id = db('student')->where('stu_className', $class_name)->where('signinFlag', 0)->order('stu_numBer')->column('stu_id');
                }
            } else {
                $stu_id = $this->stu->getWeekSign($select, $class_name, $search);
            }


            $unstuSign = array();

            foreach ($stu_id as $key => $value) {
                array_push($unstuSign, $this->stu->getSignin($value));
            }


//            foreach ($unstuSign as $key => $value) {
//                $value[0]['signInFlag'] ? $value[0]['signInFlag'] = '<span class="layui-badge layui-bg-green">是</span>' : $value[0]['signInFlag'] = '<span class="layui-badge">否</span>';
//            }

            foreach ($unstuSign as $key => $value) {
                $stu_id = db('student')->where('stu_numBer', $value['stu_numBer'])->value('stu_id');

                $value['sendtime'] = date('Y-m-d h:i:s', $value['sendtime']);
                $value['stu_id'] = $stu_id;

                if (!$value['address']) {
                    $value['address'] = '<span class="layui-badge">没有签到记录</span>';
                    $value['sendtime'] = '<span class="layui-badge">没有签到记录</span>';
                }
            }

            return json($unstuSign);

        }

        $weekArr = $this->getWeekCount();

        $this->assign('class_id', $class_id);
        $this->assign('weekArr', $weekArr);
        return $this->fetch();
    }

    public function getWeekCount()
    {
        $weekTime = (new Config())->where('config_key', 'week')->value('config_val');
        $startWeek = date('W', $weekTime);
        $endWeek = date('W', time());

        $len = $endWeek - $startWeek;

        $week = [];

        for ($i = 0, $weekNumBer = (int)$startWeek; $i <= $len; $i++, $weekNumBer++) {

            $week[$i] = [
                "weekShow" => $i,
                "weekNumBer" => $weekNumBer,
            ];

        }

        return $week;
    }

    public function logsinfo()
    {
        $class_id = input('get.class_id');

        if (request()->isAjax()) {
            $class_id = input('get.class_id');
            $curr = input('get.curr');
            $limit = input('get.limit');
            $changge = input('get.changge');
            $select = input('post.select');
            $sea = input('post.sea');

            $class_name = db('class')->where('class_id', $class_id)->value('class_name');

            $logs = $this->stu->getLogs($class_name, $changge);

            $select = $select;

            $sea = $sea;

            if ($select) {
                if ($sea) {
                    if ($changge) {
                        $logs = $this->stu->search($class_name, $sea, $changge);
                    } else {
                        $logs = $this->stu->search($class_name, $sea, $changge);
                    }
                }

                foreach ($logs as $key => $value) {
                    if (date('m', $value['sendtime']) != $select) {
                        unset($logs[$key]);
                    }
                }
            } else {
                if ($sea) {
                    if ($changge) {
                        $logs = $this->stu->search($class_name, $sea, $changge);
                    } else {
                        $logs = $this->stu->search($class_name, $sea, $changge);
                    }
                }
            }



            foreach ($logs as $key => $value) {
                $str = $value['logs_content'];
                $str = mb_substr(strip_tags($str), 0, 15, 'utf-8');
                $value['logs_content'] = $str;
                $value['sendtime'] = date('Y-m-d h:i:s', $value['sendtime']);

                if ($value['replyFlag'] == 0) {

                    $value['replyFlag'] = '<span class="layui-badge">未评阅</span>';

                } else if ($value['replyFlag'] == 1) {

                    $value['replyFlag'] = '<span class="layui-badge layui-bg-green">已评阅</span>';

                } else {

                    $value['replyFlag'] = '<span class="layui-badge">不合格</span>';

                }
            }


//            if ($curr) {
//                $star = ($curr - 1) * $limit;
//                $logs = array_slice($logs, $star, $limit);
            return json($logs);
//            } else {
//                return sizeof($logs);
//            }

        }
        $this->assign('class_id', $class_id);
        return $this->fetch();
    }


    public function lookLogs()
    {
        $id = session('tch.tch_id');
        $class_id = input('get.class_id');
        $authority = $this->tch->getAuthority($id);
        $logs_id = input('get.id');
        $noRepay = input('get.noRepay');


        $logs = db('logs')->where('logs_id', $logs_id)->find();

        $stu_name = db('student')->where('stu_id', $logs['stu_id'])->value('stu_name');

        $this->assign('authority', $authority);
        $this->assign('logs', $logs);
        $this->assign('stu_name', $stu_name);
        $this->assign('class_id', $class_id);
        $this->assign('noRepay', $noRepay);
        return $this->fetch();
    }

    public function replyAll()
    {
        $class_id = input('get.class_id');
        $class_name = db('class')->where('class_id', $class_id)->value('class_name');
        $logs_id = $this->cla->getreply($class_name);

        if (!sizeof($logs_id)) {
            return $res = ['valid' => 0, 'msg' => '没要未评阅的日志!'];
        } else {
            return $res = ['valid' => 1];
        }
    }

    public function reply()
    {
        $data = input('post.logs_reply');
        $replay_val = input('post.replay_val');
        $logs_id = input('get.logs_id');
        $class_id = input('get.class_id');
        $class_name = db('class')->where('class_id', $class_id)->value('class_name');

        if (!$logs_id) {
            $len = strlen($data);
            if ($len > 15000) {
                $res = ['valid' => 0, 'msg' => '评阅字数过多!'];
            } else {
                $logs_id = $this->cla->getreply($class_name);

                foreach ($logs_id as $key => $value) {

                    $res = db('logs')->where('logs_id', $value)->update(['logs_reply' => $data, 'replyFlag' => 1, 'replay_val' => $replay_val]);

                    if ($res) {
                        $res = true;
                    } else {
                        $res = false;
                    }
                }
                if ($res) {
                    $res = ['valid' => 1, 'msg' => '评阅成功!'];
                } else {
                    $res = ['valid' => 0, 'msg' => '评阅失败!'];
                }

            }

        } else {
            $len = strlen($data);


            if ($len > 15000) {
                $res = ['valid' => 0, 'msg' => '评阅字数过多!'];
            } else {
                $stu = db('logs')->where('logs_id', $logs_id)->find();

                if ($replay_val == '不合格') {
                    (new Stu())->save([
                        'logsFlag' => '0',
                    ], ['stu_id' => $stu['stu_id']]);

                    $res = db('logs')->where('logs_id', $logs_id)->update(['logs_reply' => $data, 'replyFlag' => 2, 'replay_val' => $replay_val]);


                } else {

                    $res = db('logs')->where('logs_id', $logs_id)->update(['logs_reply' => $data, 'replyFlag' => 1, 'replay_val' => $replay_val]);

                }

                if ($res) {
                    $res = ['valid' => 1, 'msg' => '评阅成功!'];
                } else {
                    $res = ['valid' => 0, 'msg' => '评阅失败!'];
                }

            }
        }

        return $res;

    }

    /**
     * 日志图片上传
     */
    public function upload()
    {
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                return json($res = ['status' => 1, 'url' => '/' . 'uploads' . '/' . $info->getSaveName()]);
            } else {
                echo $file->getError();
            }
        }
        return json($res = ['status' => 0, 'msg' => '图片上传错误']);
    }


    public function unlogs()
    {
        $class_id = input('get.class_id');
        if (request()->isAjax()) {
            $class_id = input('get.class_id');
//            $curr = input('get.curr');
//            $limit = input('get.limit');
            $changge = input('get.changge');
            $search = input('post.sea');
            $select = input('post.select');


            $class_name = db('class')->where('class_id', $class_id)->value('class_name');

            if ($select) {
                $stu_id = $this->stu->getUnLogstStu($select, $search, $class_name);

            } else {
                if ($search) {

                    $stu_id = $this->stu->searchLogs($class_name, $search);

                } else {

                    $stu_id = db('student')->where('stu_className', $class_name)
                        ->where('logsFlag', 0)
                        ->order('stu_numBer')
                        ->column('stu_id');

                }
            }


            $logs = [
                '0' => [],
            ];



            foreach ($stu_id as $key => &$value) {

                if ($select) {
                    $stu = db('student')
                        ->where('stu_id', $value)
                        ->find();
                } else {
                    $stu = db('student')
                        ->where('stu_id', $value)
                        ->where('logsFlag', 0)
                        ->find();
                }

                $l = $this->stu->getunLogs($value, $select);

                if (sizeof($l) > 0) {

                    $l = $this->stu->getunLogs($value, $select);
                    $l = $l[0];


                    if ($l['sendtime']) {
                        $l['sendtime'] = date('Y-m-d', $l['sendtime']);
                    }

                    $str = $l['logs_content'];

                    if ($str) {
                        $str = mb_substr(strip_tags($str), 0, 15, 'utf-8');
                    }

                    $l['logs_content'] = $str;

                    if (!$l['logs_id']) {
                        $l['logs_content'] = '<span class="layui-badge">没有日志记录</span>';
                        $l['sendtime'] = '<span class="layui-badge">没有日志记录</span>';

                    }


                    array_push($logs[0], $l);

                } else {

                    $stu['sendtime'] = '';
                    $stu['logs_content'] = '';
                    $stu['logs_id'] = '';
                    $stu['stu_numBer'] = $stu['stu_numBer'];

                    if (!$stu['logs_id']) {
                        $stu['logs_content'] = '<span class="layui-badge">没有日志记录</span>';
                        $stu['sendtime'] = '<span class="layui-badge">没有日志记录</span>';

                    }

                    $logs[0][$key] = $stu;

                }

            }

//            return json();


            if (sizeof($logs) > 0) {

                foreach ($logs[0] as $key => &$value) {

//                    $stu_id = db('student')->where('stu_numBer', $value['stu_numBer'])->value('stu_id');
//
//
//
//                    $value['stu_id'] = $stu_id;
//
//                    if ($value['sendtime']) {
//                        $value['sendtime'] = date('Y-m-d', $value['sendtime']);
//                    }
//
//                    $str = $value['logs_content'];
//
//                    if ($str) {
//                        $str = mb_substr(strip_tags($str), 0, 15, 'utf-8');
//                    }
//
//                    $value['logs_content'] = $str;
//
//                    if (!$value['logs_id']) {
//                        $value['logs_content'] = '<span class="layui-badge">没有日志记录</span>';
//                        $value['sendtime'] = '<span class="layui-badge">没有日志记录</span>';
//
//                    }

                }
            } else {
                $logs = [
                    '0' => [],
                ];
            }

//            if ($curr) {
//                $star = ($curr - 1) * $limit;
//                $logs = array_slice($logs, $star, $limit);

            return json($logs[0]);
//            } else {
//                return sizeof($logs[0]);
//            }
        }

        $this->assign('class_id', $class_id);
        return $this->fetch();
    }


    public function score()
    {
        $class_id = input('get.class_id');

        $class_name = db('class')->where('class_id', $class_id)->value('class_name');

        $all = db('student')->where('stu_className', $class_name)->select();
        if (sizeof($all)) {
            $scoreFlag = db('class')->where('class_id', $class_id)->value('scoreFlag');
        } else {
            $scoreFlag = 0;
        }


        if (request()->isAjax()) {

            $class_id = input('get.class_id');
            $curr = input('get.curr');
            $limit = input('get.limit');
            $search = input('post.sea');

            $class_name = db('class')->where('class_id', $class_id)->value('class_name');

            $stuInfo = db('student')->where('stu_className', $class_name)->select();
            if ($search) {
                $stuInfo = $this->stu->sreachScore($search, $class_name);
            } else {
                $stuInfo = db('student')->where('stu_className', $class_name)->select();
            }

            foreach ($stuInfo as $key => $value) {
                $logsSum = db('logs')->where('stu_id', $value['stu_id'])->select();
                $signinSum = db('sginin')->where('stu_id', $value['stu_id'])->select();

                if (!sizeof($logsSum)) {
                    $stuInfo[$key]['logsSum'] = '<span class="layui-badge">' . sizeof($logsSum) . '</span>';
                } else {
                    $stuInfo[$key]['logsSum'] = sizeof($logsSum);
                }

                if (!sizeof($signinSum)) {
                    $stuInfo[$key]['signinSum'] = '<span class="layui-badge">' . sizeof($signinSum) . '</span>';
                } else {
                    $stuInfo[$key]['signinSum'] = sizeof($signinSum);
                }

                if (!$value['stu_score']) {
                    $stuInfo[$key]['stu_score'] = '<span class="layui-badge" style="height: 100%;">双击评分</span>';
                }
            }

            if ($curr) {
                $star = ($curr - 1) * $limit;
                $stuInfo = array_slice($stuInfo, $star, $limit);
                return json($stuInfo);
            } else {
                return sizeof($stuInfo);
            }
        }

        $this->assign('class_id', $class_id);
        $this->assign('scoreFlag', $scoreFlag);
        return $this->fetch();
    }

    public function addScore()
    {
        $stu_id = input('get.stu_id');
        $data = input('post.');

        $result = $this->validate(
            [
                '评分' => $data['data'],
            ],
            [
                '评分' => 'require|number|between:0,100',
            ]);
        if (true !== $result) {
            return json($res = ['valid' => 0, 'msg' => $result]);
        }

        $res = db('student')->where('stu_id', $stu_id)->update(['stu_score' => $data['data']]);

        if ($res) {
            $res = ['valid' => 1, 'msg' => '评分成功!'];
        } else {
            $res = ['valid' => 0, 'msg' => '评分失败!'];
        }
        return json($res);
    }

    public function submit()
    {
        $class_id = input('get.class_id');
        $class_name = db('class')->where('class_id', $class_id)->value('class_name');

        $all = db('student')->where('stu_className', $class_name)->where('stu_score', null)->select();

        if (!$all) {
            $res = db('class')->where('class_id', $class_id)->update(['scoreFlag' => 1]);
            if ($res) {
                $res = ['valid' => 1, 'msg' => '提交成功!'];
            } else {
                $res = ['valid' => 0, 'msg' => '提交失败!'];
            }
        } else {
            $res = ['valid' => 0, 'msg' => '还有未评分的学生!'];
        }


        return json($res);
    }
}