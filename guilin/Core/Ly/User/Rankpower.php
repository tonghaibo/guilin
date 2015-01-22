<?php
/**
 * 用户等级权限表管理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-14
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User_Rankpower{
	public static function insert($data){
		if(empty($data)) return false;
		$model = Y_Db::init('user_rankpower');
		return $model->insert($data);
	}
	public static function update($id,$data){
		if(!$id or empty($data)) return false;
		$model = Y_Db::init('user_rankpower');
		return $model->where(array('id'=>$id))->update($data);
	}
	//获取列表
	public static function getList(){
		$model = Y_Db::init('user_rankpower');
		return $model->select();
	}
	//获取值
	public static function getInfo($id){
		if(empty($id)) return false;
		$model = Y_Db::init('user_rankpower');
		$rank = $model->find($id);
		if($rank){
			return $rank;
		}else{
			return false;
		}
	}
}
?>
