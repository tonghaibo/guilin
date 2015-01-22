<?php
/**
 * 首页测试
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class indexController extends Y_Controller{
	//初始化
	public function __construct(){
		$web = Y_Db::init('seos');
		$str = Y::$controller.'_'.Y::$action;
		$web->where(array('name'=>$str));
		$web->field(array('title','author','keywords','des'));
		$this->assign('web',$web->find());
	}
	public function indexAction(){
		$this->display('index');
	}
	//注册
	public function regAction(){
		if(Ly_User::isLogin()){
			$this->error('您已经登录，请不要重复注册','index.html',3);
			die;
		}
		//注册
		$this->display('reg',false);
	}
	//登录
	public function loginAction(){
		if(Ly_User::isLogin()){
			$this->error('您已经登录，请不要重复登录','index.html',3);
			die;
		}
		//注册
		$this->display('login',false);
	}
	//登录验证
	public function logininAction(){
		if(Ly_User::isLogin()){
			$this->error('您已经登录，请不要重复登录','index.html',3);
			die;
		}
		$data['loginName'] = $this->post('loginName');
		$data['password'] = md5($this->post('password'));
		if($this->post('agreement')){
			$num = Ly_User::login($data,true);
		}else{
			$num = Ly_User::login($data,false);
		}
		$url = Y::Config('sys','domain');
		$rel = $this->post('referer_url');
		if($rel && $u=parse_url($rel)){
			if($u['host']==$_SERVER['HTTP_HOST']){
				$url = $rel;
			}
		}
		switch($num){
			case 1:
				$this->error('该用户不存在或未注册','index_login.html',3);
				break;
			case 2:
				$this->error('您输入的密码和帐号不相符合','index_login.html',3);
				break;
			case 3:
				$this->error('该帐号因违反相关规定暂时被冻结，请联系客服！','index.html',3);
				break;
			case 4:
			case 5:
				$this->Go($url);
				break;
			default:
				$this->error('系统未知错误','index_login.html',3);
				break;

		}
	}
	public function checkAction(){
		//判断是否是ajax提交
		if($this->isAjax()){
			$name = $this->post('loginName');
			/*if(is_numeric($name)){
				//手机
				$check = Ly_User::check($name,false);
			}else{
				//邮箱
				$check = Ly_User::check($name);
			}*/
			//邮箱
			$check = Ly_User::check($name);
			if($check){
				echo 1;
			}else{
				echo 0;
			}
			//var_dump($_POST['loginName']);
			die;
		}else{
			$this->error('禁止访问','index.html',3);
		}
	}
	//验证码生成
	public function vcodeAction(){
		Y_Image_Verify::Verify(5,'png',100,30);
	}
	//注册传值
	public function regixuserAction(){
		if(Ly_User::isLogin()){
			$this->error('您已经登录，请不要重复注册！','index.html',3);
			die;
		}
		$vcode = strtoupper($this->post('code'));
		//验证码验证
		if(Y_Session::get('verify')!=strtoupper($vcode)){
			$this->error('您输入的验证码有误','index_reg.html',3);
			die;
		}
		//协议是否同意
		if(!$this->post('agree')){
			$this->error('您还未同意游桂林服务相关协议，不能够注册','index_reg.html',3);
			die;
		}
		$name = $this->post('loginName');
		//以后的手机注册
		if(is_numeric($name)){
			//echo $name;
			$this->error('系统暂且不支持手机注册','index_reg.html',3);
		}else{
			$data = array();
			
			//邮箱注册
			if(Y_Check::email($name)){
				//是否已经注册了
				if(Ly_User::check($name)){
					$this->error('该邮箱已经被注册了！','index_reg.html',3);
					die;
				}
				$data['email'] = $name;
				$data['name'] = array_shift(explode('@',$name));
			}else{
				$this->error('注册邮箱格式不正确','index_reg.html','3');
				die;
			}
			//密码验证
			$password = $this->post('password');
			if(Y_Check::password($password)){
				$data['password'] = md5($password);
			}else{
				$this->error('您的密码格式输入不正确','index_reg.html',3);
				die;
			}
			if(Ly_User::regix($data)){
				//$this->error('恭喜您注册成功，');
				//发送邮件
				//$this->sendmailAction();
				Ly_Mail::send($data['email']);
				//推广
				if($d=$this->post('reguid')){
					Ly_User_Ranklog::setLog($d,18);
				}
				$this->Go('index_regok.html&email='.$data['email']);
			}else{
				$this->error('对不起！系统错误，注册失败！','index_reg.html',3);
			}
		}
	}
	//注册成功页
	public function regokAction(){
		$email = $this->get('email');
		$name = explode('@',$email);
		$this->assign('email',$email);
		$this->assign('url','http://mail.'.$name[1]);
		$this->display('regok',false);
	}
	//邮件验证
	public function rzemailAction(){
		if($uid=Y_Session::get('uid')){
			$email = Ly_User::getEmail($uid);
			Ly_Mail::send($email,'',4);
			$name = explode('@',$email);
			$this->assign('email',$email);
			$this->assign('url','http://mail.'.$name[1]);
			$this->display('rzemail',false);
		}else{
			$this->error('请先登录！','index_login.html',3);
		}
	}
	//邮件发送
	public function sendmailAction(){
		if($this->isAjax()){
			$email = $this->post('mail');
			$type = $this->post('tempnum');
			$type = intval($type);
			if($type==1 || $type==2 || $type==4){
				if(Y_Check::email($email)){
					$num=Ly_Mail::send($email,'',$type);
					$data['status'] = intval($num);
				}else{
					$data['status'] = 1;
				}
			}else{
				$data['status'] = 1;
			}
			echo json_encode($data);
			die;
		}else{
			//非法
			$this->error('禁止访问','index.html',3);
		}	
	}
	//邮件激活
	public function emailactAction(){
		$data['uid'] = $this->get('id');
		$data['email'] = $this->get('email');
		$data['token'] = $this->get('token');
		if(Ly_Mail::check($data)){
			if($this->get('t')){
				$this->success('恭喜您，您的邮箱已经成功验证！','home.html',3);
			}else{
				$this->success('恭喜您，您的帐号激活成功！','home_base.html',3);
			}		
		}else{
			$this->assign('email',$data['email']);
			$this->display('emailact');
		}
	}
	//退出登录
	public function loginoutAction(){
		Y_Session::destory();
		//cookie也清除
		Y_Cookie::delete('uid');
		Y_Cookie::delete('name');
		Y_Cookie::delete('grade');
		Y_Cookie::clear();
		$this->success('您已经成功退出登录！','index.html',3);
	}
	//返回登录数据
	public function getmyinfoAction(){
		if($this->isAjax()){
			$data = Ly_User::isLogin();
			$info = Ly_User::getBaseinfo($data['uid']);
			$notice = 0;
			$mess = 0;
			if($data['uid']){
			$notice = Ly_User::getNotice($data['uid']);
			$mess = Y_Db::init('message');
			$mess = $mess->where(array('isread'=>0,'status<'=>1,'todel'=>0,'touid'=>$data['uid']))->count();
			}
			$this->assign('info',$info);
			$this->assign('notice',$notice);
			$this->assign('mess',$mess);
			$this->display('getmyinfo',false);
		}
		die;
	}
	//找回密码
	public function getpassAction(){
		if(Ly_User::isLogin()){
			$this->error('您已经登录!','index.html',3);
			die;
		}
		//预定义为空
		$this->assign('error','');
		$name = $this->post('loginName')?$this->post('loginName'):'';
		$vcode = $this->post('code')?$this->post('code'):'';
		$this->assign('name',$name);
		$this->assign('vcode',$vcode);
		if($name){
			//验证码验证
			if(Y_Session::get('verify')!=strtoupper($vcode)){
				$this->assign('error',7);
				$this->display('getpass',false);
			}else{
				//是否邮箱合法
				if(Y_Check::email($name)){
					if(Ly_User::check($name)){	
						//成功
						//发送邮件
						Ly_Mail::send($name,'',2);
						$names = explode('@',$name);
						$this->assign('email',$name);
						$this->assign('url','http://mail.'.$names[1]);
						$this->display('passok',false);
					}else{
						//用户不存在
						$this->assign('error',4);
						$this->display('getpass',false);
					}
				}else{
					//格式不对
					$this->assign('error',1);
					$this->display('getpass',false);
				}	
			}
			
			
		}else{
			
			$this->display('getpass');	
		}
		
	}
	//密码重置
	public function setpassAction(){
		if(Ly_User::isLogin()){
			$this->error('您已经登录!','index.html',3);
			die;
		}
		$data['email'] = $this->get('email');
		$data['token'] = $this->get('token');
		$data['uid'] = $this->get('id');
		//验证链接是否有效
		if(Ly_Mail::check($data)){
			//激活后直接跳转
			$this->assign('uid',$data['uid']);
			$this->display('setpass',false);
		}else{
			$this->display('passerror',false);
		}
	}
	//修改密码
	public function uppassAction(){
		if(Ly_User::isLogin()){
			$this->error('您已经登录!','index.html',3);
			die;
		}
		$vcode = $this->post('code');
		//验证码验证
		if(Y_Session::get('verify')!=strtoupper($vcode)){
			$this->error('您输入的验证码有误','index_setpass.html',3);
			die;
		}
		//验证码输入正确
		$password = $this->post('password');
		if(Y_Check::password($password)){
			$data['password'] = md5($password);
		}else{
			$this->error('您的密码格式输入不正确','index_setpass.html',3);
			die;
		}
		if($uid=$this->post('tokenid')){
			$model = Y_Db::init('user');
			if($model->where(array('uid'=>$uid))->update($data)){
				$this->success('修改密码成功','index_login.html',3);
			}else{
				$this->error('修改密码失败','index_setpass.html',3);
			}
			
		}else{
			$this->error('非法提交','index.html',3);
			die;
		}
	}
	//意见反馈
	public function feedbackAction(){
		if($this->isAjax()){
			$data['contents'] = $this->post('content');
			if($data['contents']){
				$data['url'] = $this->post('url');
				Ly_Feedback::insert($data);
				echo 0;
			}else{
				echo 1;
			}
		}
		die;
	}
}
?>
