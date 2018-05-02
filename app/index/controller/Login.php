<?php
namespace app\index\controller;
use think\Controller;
use think\Dd;

class Login extends Controller{
	public function index() {
		return $this->fetch();
	}

	public function login(){
		$stu_numBer = input('post.stu_numBer');
		$password = input('post.stu_password');
		$data = [
		    'stu_numBer' => $stu_numBer,
		    'stu_password'=> $password,
		    'verification' => input('post.verification'),
		];
		$validate = validate('Login');
		if(!$validate->check($data)){
			$res = ['valid' => 0, 'msg' => $validate->getError()];
		} else {
		    $info =  Db('student')->where('stu_numBer', $stu_numBer)
		    ->where('stu_password', md5($password))->find();

		    if ($info) {
		    	session('stu', ['stu_id' => $info['stu_id'], 'stu_name' => $info['stu_name']]);
		    	$res = ['valid' => 1, 'msg' => '登录成功!'];
		    } else {
		    	$res = ['valid' => 0, 'msg' => '账号或密码不正确!'];
		    }
		}
		if($res['valid'])
		{
			return json($res);
		}else{
			//说明登录失败
			return json($res);
		}
	}


	public function editPsw() {
		return $this->fetch();
	}

	public function editPswFn(){
		$data = input('post.');
		$validate = validate('app\index\validate\editPsw');
		if (!$validate->check($data)) {
			$res = ['valid' => 0, 'msg' => $validate->getError()];
		} else {
			$info =  Db('student')->where('stu_name', $data['stu_name'])
			->where('stu_numBer', $data['stu_numBer'])
		    ->where('identity', $data['identity'])->find();
		    if($info) {
		    	if ($data['oldPsw'] == $data['newPsw']) {
		    		 Db('student')->where('stu_numBer', $data['stu_numBer'])->update(['stu_password' => md5($data['oldPsw'])]);
		    		$res = ['valid' => 1, 'msg' => '修改成功!'];
		    	} else {
		    		$res = ['valid' => 0, 'msg' => '两次密码不一致!'];
		    	}
		    } else {
		    	$res = ['valid' => 0, 'msg' => '信息填写错误,请重新填写!'];
		    }
		}

		if($res['valid'])
		{
			return json($res);
		}else{
			return json($res);
		}
	}

}