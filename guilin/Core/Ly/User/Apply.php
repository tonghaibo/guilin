<?php
/**
 * 用户申请表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-14
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User_Apply{
	//获取列表 2酒店 3线路
	public static function getList($num=10,$gid=3){
		$model = Y_Db::init('user_apply');
		$model->field(array('uid','cpname','cptel'));
		$model->where(array('status'=>1,'gid'=>$gid));
		$model->limit($num);
		$model->order('id DESC');
		return $model->select();
	}
	//获取对应名称
	public static function getName($uid,$gid=3,$name='cpname'){
		if(empty($uid)) return false;
		$model = Y_Db::init('user_apply');
		$rank = $model->field($name)->find($id);
		if($rank){
			return $rank[$name];
		}else{
			return false;
		}
	}
}
?>
