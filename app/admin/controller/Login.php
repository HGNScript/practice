<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\captcha;

class Login extends Controller {
	public function index() {
		if(request()->isPost()){
			$username = input('post.admin_username');
			$password = input('post.admin_password');
			$data = [
			    'admin_username' => $username,
			    'admin_password'=> $password,
			    'code' => input('post.code'),
			];
			$validate = validate('Login');

			if(!$validate->check($data)){
				$res = ['valid' => 0, 'msg' => $validate->getError()];
			} else {
			    $info =  Db('admin')->where('admin_username', $username)
			    ->where('admin_password', md5($password))->find();

			    if ($info) {
			    	session('admin', ['admin_id' => $info['admin_id'], 'admin_username' => $info['admin_username']]);
			    	$res = ['valid' => 1, 'msg' => '登录成功!'];
			    } else {
			    	$res = ['valid' => 0, 'msg' => '用户名或密码不正确!'];
			    }
			}
			if($res['valid'])
			{
				//说明登录成功
				$this->success($res['msg'],'Index/index');exit;
			}else{
				//说明登录失败
				$this->error($res['msg']);exit;
			}
		}
		//加载我们登录页面
		return $this->fetch();
	}
}