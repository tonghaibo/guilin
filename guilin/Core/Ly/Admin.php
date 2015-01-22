<?php
/**
 * 管理员用户操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-30
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Admin{
	public static $model;
	private static function init(){
		if(!self::$model){
			self::$model = Y_Db::init('admin');
		}
		return self::$model;
	}
	//管理员添加
	public static function insert($data){
		if(!is_array($data) || empty($data)) return false;
		$model = self::init();
		return $model->insert($data);

	}
	//用户名检测
	public static function check($name){
		$model = self::init();
		$model->field(array('id'));
		return $model->where(array('name'=>$name))->find();
	}
	//登录
	public static function login($data){
		if(is_array($data) && $data){
			$model = self::init();
			$model->field(array('id','name','realname','password','groupid','flag'));

			$model->where(array('name'=>$data['name']));
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
			if($dd['flag']!=1){
				return 3;
			}
			Y_Session::set('G_name',$dd['realname'].'('.$dd['name'].')');
			Y_Session::set('G_id',$dd['id']);
			Y_Session::set('G_gid',$dd['groupid']);
			Y_Session::set('G_Power',Ly_Group::getGpower($dd['groupid'],$dd['id']));
			//正确
			return 4;

		}else{
			return 1;
		}
	}
	//判断是否登录
	public static  function isLogin(){
		if(Y_Session::get('G_id')){
			//更新状态
			Ly_Session::setAdmin();
			$data['G_id'] = Y_Session::get('G_id');
			$data['G_name'] = Y_Session::get('G_name');
			$data['G_gid'] = Y_Session::get('G_gid');
			$data['G_Power'] = Y_Session::get('G_Power');
			return $data;
		}
		return false;
	}
	//获取名称
	public static function getName($id){
		if(empty($id)) return false;
		$model = self::init();
		$model->where(array('id'=>$id));
		$model->field(array('name','realname'));
		$info = $model->find();
		if($info){
			$name = $info['realname'].'('.$info['name'].')';
			return $name;
		}
		return false;
	}
	//获取ID
	public static function getUid($name){
		if(empty($name)) return false;
		$model = self::init();
		$model->where(array('name'=>$name));
		$model->field(array('id'));
		$info = $model->find();
		if($info){	
			return $name['id'];
		}
		return false;
	}
	//根据ID列表获取
	public static function getNames($ids){
		if(empty($ids)) return false;
		$model = self::init();
		$model->field(array('id','name','realname'));
		return $model->select($ids);
	}
	//获取列表
	public static function getList(){
		$model = self::init();
		$model->field(array('id','name','realname','groupid','flag'));
		return $model->select();
	}
	//获取信息
	public static function getInfo($id){
		if(empty($id)) return false;
		$model = self::init();
		$model->field(array('id','name','realname','groupid','flag'));
		return $model->find($id);
	}
	//更新
	public static function update($id,$data){
		if(empty($id) or empty($data)) return false;
		$model = self::init();
		$model->where(array('id'=>$id));
		return $model->update($data);
	}
	//删除
	public static function delete($id){
		if(empty($id)) return false;
		$model = self::init();
		return $model->delete($id);
	}
	//获取密码
	public static function getPassword($uid=0){
		if($uid==0){
			$uid = Y_Session::get('G_id');
		}
		$model = self::init();
		$model->field('password');
		if($data=$model->find($uid)){
			return $data['password'];
		}
		return false;
	}
}
?>
