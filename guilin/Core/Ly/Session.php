<?php
/**
 * session表操作
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-10-27
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Session{
	//admin
	public static function setAdmin(){
		$model = Y_Db::init('session');
		$sid = session_id();
		$info = $model->where(array('sid'=>$sid))->find();
		//10秒的不更新
		if($info['uptime']>(time()-10)){
			//不动
			return;
		}
		//120秒的退出
		/*if($info['uptime']<(time()-120)){
			//退出
			Y_Session::destory();
			return;
		}*/
		$data['gid'] = Y_Session::get('G_id');
		$data['gurl'] = $_SERVER['REQUEST_URI'];
		$data['uptime'] = time();
		$model->where(array('sid'=>$sid));
		$model->update($data);
	}
	public static function setUser($uid){
		$model = Y_Db::init('session');
		$sid = session_id();
		$data['uid'] = $uid;
		$data['uurl'] = $_SERVER['REQUEST_URI'];
		$model->where(array('sid'=>$sid));
		$model->update($data);
	}
}
?>
