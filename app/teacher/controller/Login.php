<?php
namespace app\teacher\controller;

use think\Controller;
use think\Dd;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 用户登录
     * @return [type] [description]
     */
    public function login()
    {

        $data = input('post.');
        $tch_numBer = input('post.tch_numBer');
        $password = input('post.password');

        $data = [
            'tch_numBer' => $tch_numBer,
            'password' => $password,
            'verification' => input('post.verification'),
        ];

        $validate = validate('Login');

        if (!$validate->check($data)) {

            $res = ['valid' => 0, 'msg' => $validate->getError()];

        } else {

            $tchInfo = Db('teacher')->where('tch_numBer', $tch_numBer)
                ->where('password', md5($password))->find();

            $stuInfo = Db('student')->where('stu_numBer', $tch_numBer)
                ->where('stu_password', md5($password))->find();


            if ($tchInfo) {

                session('tch', ['tch_id' => $tchInfo['tch_id'], 'tch_name' => $tchInfo['tch_name']]);

                $res = ['valid' => 1, 'msg' => '登录成功!', 'flag' => 'tch'];

            } else {

                if ($stuInfo) {

                    session('stu', ['stu_id' => $stuInfo['stu_id'], 'stu_name' => $stuInfo['stu_name']]);

                    $res = ['valid' => 1, 'msg' => '登录成功!', 'flag' => 'stu'];

                } else {

                    $res = ['valid' => 0, 'msg' => '账号或密码不正确!'];

                }
            }



        }


        if ($res['valid']) {
            return json($res);
        } else {
            return json($res);
        }

    }

    public function editpsw()
    {
        if (request()->isAjax()) {
            $data = input('post.');
            $numBer = $data['tch_numBer'];
            $phone = $data['tch_phone'];

            $validate = validate('EditLogin');

            if ($validate->check($data)) {
                $info = db('teacher')->where('tch_numBer', $numBer)->where('tch_phone', $phone)->find();
                if ($info) {
                    if ($data['newtch_psw'] == $data['newtch_psws']) {
                        $suc = db('teacher')->where('tch_numBer', $numBer)->update(['password' => md5($data['newtch_psw'])]);
                        if ($suc) {
                            return json($res = ['valid' => 1, 'msg' => '重置密码成功!']);
                        } else {
                            return json($res = ['valid' => 0, 'msg' => '重置密码失败!']);
                        }
                    } else {
                        return json($res = ['valid' => 0, 'msg' => '两次密码不一致']);
                    }
                } else {
                    return json($res = ['valid' => 0, 'msg' => '工号或电话号码不一致']);
                }
            } else {
                return json($res = ['valid' => 0, 'msg' => $validate->getError()]);
            }

        }
        return $this->fetch();
    }


}