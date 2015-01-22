<?php
/**
 * 公共的登录注册和退出
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-12
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class privateController extends Y_Controller{
	public function loginAction(){
		if(!Ly_User::isLogin()){
			$this->Go('index.php?ac=login');
		}
		if(Y_Session::get('G_id')){
			$this->error('已经登录系统，请不要重复登录系统！');
			die;
		}
		$error = '';
		if($name=$this->post('name')){
			//判断vocde
			if(strtoupper($this->post('vcode'))!=Y_Session::get('verify')){
				$error = 5;
			}else{
				$error = Ly_Admin::login(array('name'=>$name,'password'=>md5($this->post('password'))));
				//登录成功
				if($error==4){
					Y_Log::write(array(Y_Session::get('G_name'),'登录系统'),'syslogin',0);
					$this->Go('govern.php');
					return;	
				}
				Y_Log::write(array($name,'登录失败,尝试密码：'.substr($_POST['password'],0,-3).'***'),'syslogin',1);
			}

		}
		$this->assign('error',$error);
		$this->display('login');
	}
	public function logoutAction(){
		Y_Log::write(array(Y_Session::get('G_name'),'退出系统'),'syslogin',0);
		Y_Session::destory();
		$this->success('您已经成功退出登录！','index.html',3);
	}
}
?>
