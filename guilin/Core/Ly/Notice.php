<?php
/**
 * 消息表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Notice{
	//上传插入
	public static function insert($data){
		if($data){
			$data['pubtime'] = time();
			$model = Y_Db::init('notice');
			return $model->insert($data);
		}
		return false;
	}
	//删除
	public static function delete($id){
		if($uid){
			$model = Y_Db::init('notice');
			return $model->where(array('id'=>$id))->delete();
		}
		return false;	
	}

}
?>
