<?php
/**
 * 用户信息配置
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-14
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User_Messset{
	public static function insert($data){
		if(empty($data)) return false;
		$model = Y_Db::init('user_messset');
		return $model->insert($data);
	}
	public static function update($id,$data){
		if(!$id or empty($data)) return false;
		$model = Y_Db::init('user_messset');
		return $model->where(array('uid'=>$id))->update($data);
	}
	//获取列表
	public static function getInfo($uid){
		$model = Y_Db::init('user_messset');
		$user = $model->find($uid);
		if($user){
			return $user;
		}else{
			return array('uid'=>$uid,'comments'=>1,'replay'=>1,'attention'=>1,'mail'=>1,'mess'=>1);
		}
	}
}
?>
