<?php
/**
 * SEO操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Feedback{
	//上传插入
	public static function insert($data){
		if($data){
			$data['uid'] = Y_Session::get('uid');
			$data['pubtime'] = time();
			$data['ip'] = Y_Client::ip();
			$data['browser'] = Y_Client::browser();
			$data['os'] = Y_Client::os();
			$model = Y_Db::init('feedback');
			return $model->insert($data);
		}
		return false;
	}
	//删除
	public static function delete($id){
		if($id){
			$model = Y_Db::init('feedback');
			return $model->where(array('id'=>$id))->delete();
		}
		return false;
	}
	//获取所有
	public static function getInfo($id){
		if($id){
			$model = Y_Db::init('feedback');
			return $model->where(array('id'=>$id))->find();
		}
		return false;
	}
}
?>
