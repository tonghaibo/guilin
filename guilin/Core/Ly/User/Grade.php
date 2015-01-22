<?php
/**
 * 管理员表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-04
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User_Grade{
	protected static $model;
	protected static function init(){
		if(!self::$model){
			self::$model = Y_Db::init('user_grade');
		}
		return self::$model;
	}
	//获取列表
	public static function getList(){
		$model = self::init();
		return $model->select();
	}
	//插入
	public static function insert($data){
		if(empty($data)) return false;
		$model = self::init();
		return $model->insert($data);
	}
	//获取信息
	public static function getInfo($gid){
		if(empty($gid)) return false;
		$model = self::init();
		return $model->find($gid);
	}
	//update
	public static function update($gid,$data){
		if(empty($gid)) return false;
		$model = self::init();
		return $model->where(array('gradeid'=>$gid))->update($data);
	}
	//获取组名
	public static function getName($gid){
		$gid = intval($gid);
		$model = self::init();
		$model->field('name');
		$info = $model->find($gid);
		if($info){
			return $info['name'];
		}
		return false;
	}
}
?>
