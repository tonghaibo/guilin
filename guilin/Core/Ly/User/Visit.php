<?php
/**
 * 用户访问记录
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-14
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User_Visit{
	public static function insert($data){
		if(empty($data)) return false;
		$data['pubtime'] = time();
		$model = Y_Db::init('user_visit');
		return $model->insert($data);
		//删除最近一个月的
		return $model->where(array('pubtime'=>(time()-25920000)))->delete();
	}
	//获取列表 true我访问的 false访问我的
	public static function getList($uid,$flag=true,$limit=0){
		$model = Y_Db::init('user_visit');
		if($flag){
			$where['uid'] = $uid;
			$group = 'visituid';

		}else{
			$where['visituid'] = $uid;
			$group = 'uid';
		}
		if($limit){
			$model->limit($limit);
		}
		return $model->where($where)->order('pubtime desc')->group($group)->select();
	}
	//记录访问$uid被访问 
	public static function visit($vuid,$uid=null){
		if($vuid){
			$uid = $uid?$uid:Y_Session::get('uid');
			if($vuid!=$uid){
			$model = Y_Db::init('user_visit');
			//删除前2个月的
			$time = strtotime('-60 days');
			$model->where(array('pubtime<'=>$time))->delete();
			//删除以前记录
			$model->where(array('uid'=>$uid,'visituid'=>$vuid))->delete();
			$model->insert(array('uid'=>$uid,'visituid'=>$vuid,'pubtime'=>time()));
			}
		}
		return false;
	}
}
?>
