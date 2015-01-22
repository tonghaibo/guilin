<?php
/**
 * 用户操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-30
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User{
	public static $model;
	private static function init(){
		if(!self::$model){
			self::$model = Y_Db::init('user');
		}
		return self::$model;
	}
	//用户注册
	public static function regix($data,$session=true,$cookie=false){
		if(!is_array($data) || empty($data)) return false;
		$model = self::init();
		$data['regip'] = Y_Client::ip();
		$data['regtime'] = time();
		//5积分
		$data['rank'] = 5;
		$data['rndnum'] = substr(md5(mt_rand()),5,8);
		$data['grade'] = 0;
		if($id=$model->insert($data)){
			if($session){
				//Y_Session::set('name',$data['name']);
				//Y_Session::set('grade',0);
				Y_Session::set('uid',$id);
			}
			if($cookie){
				//Y_Cookie::set('name',$data['name']);
				//Y_Cookie::set('grade',0);
				Y_Cookie::set('uid',$id);
			}
			return true;
		}else{
			return false;
		}
	}
	//邮箱，手机号码检测
	public static function check($name,$flag=true){
		$model = self::init();
		$model->field(array('uid','name'));
		if($flag){
			return $model->where(array('email'=>$name))->find();
		}else{
			return $model->where(array('mobile'=>$name))->find();
		}
	}
	//登录
	public static function login($data,$flag=true){
		if(is_array($data) && $data){
			$model = self::init();
			$model->field(array('uid','name','password','loginip','logintime','logins','rank','grade','status'));
			if(is_numeric($data['loginName'])){
				$model->where(array('mobile'=>$data['loginName']));
			}else{
				$model->where(array('email'=>$data['loginName']));
			}
			$dd = $model->find();
			//帐号不存在
			if(empty($dd)){
				return 1;
			}
			//密码不对
			if($data['password']!=$dd['password']){
				return 2;
			}
			//用户状态被注销被删除
			if($dd['status']>1){
				return 3;
			}
			//积分记录
			Ly_User_Ranklog::setLog($dd['uid'],15);
			$up['logins'] = $dd['logins']+1;
			$up['logintime'] = time();
			$up['loginip'] = Y_Client::ip();
			$model->where(array('uid'=>$dd['uid']))->update($up);
			//Y_Session::set('name',$dd['name']);
			//Y_Session::set('grade',$dd['grade']);
			Y_Session::set('uid',$dd['uid']);
			Y_Session::set('user_dotime',time());
			if($flag){
				//Y_Cookie::set('name',$dd['name']);
				//Y_Cookie::set('grade',$dd['grade']);
				Y_Cookie::set('uid',$dd['uid']);
			}
			//IP位置问题
			if($up['loginip']!==$dd['loginip']){
				return 4;
			}
			//正确
			return 5;

		}else{
			return 1;
		}
	}
	//判断是否登录
	public static  function isLogin(){
		if(Y_Session::get('uid')){
			$data['uid'] = Y_Session::get('uid');
			//$data['name'] = Y_Session::get('name');
			//$data['grade'] = Y_Session::get('grade');
			Ly_Session::setUser($data['uid']);
			return $data;
		}
		if(Y_Cookie::get('uid')){
			$data['uid'] = Y_Cookie::get('uid');
			//加分
			$u = Y_Db::init('user');
			$u->where(array('uid'=>$data['uid'],'status<'=>1));
			$user = $u->field(array('logintime','logins','name'))->find();
			if($user){
				Y_Session::set('user_dotime',$user['logintime']);
				$up['logins'] = $user['logins']+1;
				$up['logintime'] = time();
				$up['loginip'] = Y_Client::ip();
				$u->where(array('uid'=>$data['uid'],'status<'=>1))->update($up);
				Ly_User_Ranklog::setLog($data['uid'],15);
				Y_Session::set('uid',$data['uid']);
				Ly_Session::setUser($data['uid']);
				return $data;
			}
			
		}
		return false;
	}
	//修改密码
	public static function uppassword($data,$uid){
		if(isset($data['password']) && $uid){
			$model = self::init();
			$model->where(array('uid'=>$uid))->update($data);
		}else{
			return false;
		}
	}
	//获取名称
	public static function getName($uid){
		if($uid){
			$model = self::init();
			$name = $model->field('name')->find($uid);
			return $name['name'];
		}
		return false;
	}
	//获取注册邮箱
	public static function getEmail($uid){
		if($uid){
			$model = self::init();
			$name = $model->field('email')->find($uid);
			return $name['email'];
		}
		return false;
	}
	//获取ID
	public static function getUid($email){
		if($email){
			$model = self::init();
			$name = $model->field('uid')->where(array('email'=>$email))->find();
			return $name['uid'];
		}
		return false;
	}
	//获取基本信息
	public static function getBase($uid){
		if($uid){
			$model = self::init();
			$name = $model->field(array('uid','email','name','sex','userimg','birthday','birthday_flag','cityid','rank','ranklevel','grade','birth_year'))->find($uid);
			if($name){
				$name['sex_name'] = Ly_Items::getName('user','sex',$name['sex']);
				$name['birthday'] = $name['birthday']?$name['birthday']:'未知';
				$name['birthday_flag'] = $name['birthday_flag']?'阳历':'阴历';
				$name['city_name'] = Ly_Area::getAddress($name['cityid']);
				$name['rank_name'] = Ly_User_Rank::getName($name['ranklevel']);
				$name['grade_name'] = Ly_Items::getName('user','grade',$name['grade']);
				$name['age'] = self::getAge($name['birth_year']);
			}
			return $name;
		}
		return false;
	}
	//获取积分数
	public static function getRank($uid){
		if($uid){
			$model = self::init();
			$name = $model->field('rank')->find($uid);
			return $name['rank'];
		}
		return false;
	}
	//获取积分等级
	public static function getRanklevel($uid){
		if($uid){
			$model = self::init();
			$name = $model->field('ranklevel')->find($uid);
			return $name['ranklevel'];
		}
		return false;
	}
	//获取头像
	public static function getUserimg($userimg,$size=30){
		if($userimg){
			return Y_Pr::image($userimg,$size,$size);
		}else{
			return Y_Pr::image('public/images/noavatar_185.jpg',$size,$size);
		}
	}
	//年龄
	public static function getAge($birthyear){
		if($birthyear){
			return date('Y')-intval($birthyear);
		}
		return '未知';
	}
	//获取最基本的
	public static function getBaseinfo($uid,$size=30){
		if($uid){
			$model = Y_Db::init('user');
			$name = $model->field(array('name','userimg','sex','ranklevel','grade','rzemail'))->find($uid);
			if($name){
				$name['sex_name'] = Ly_Items::getName('user','sex',$name['sex']);
				$name['grade_name'] = Ly_Items::getName('user','grade',$name['grade']);
				$name['rank_name'] = Ly_User_Rank::getName($name['ranklevel']);
				$name['userimg'] = self::getUserimg($name['userimg'],$size);
			}
			return $name;
		}
		return false;
	}
	public function getNotice($uid){
		$time = Y_Session::get('user_dotime');
		if($time){
			Y_Session::set('user_dotime',time());
			$model = Y_Db::init('notice');
			$model->where("uid in(0,{$uid}) AND status=1 AND pubtime>={$time}");
			return $model->count();
		}
		return 0;
	}
}
?>
