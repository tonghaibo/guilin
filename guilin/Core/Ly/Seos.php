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
class Ly_Seos{
	//上传插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('seos');
			return $model->insert($data);
		}
		return false;
	}
	//更新
	public static function update($name,$data){
		if($name and $data){
			$model = Y_Db::init('seos');
			return $model->where(array('name'=>$name))->update($data);
		}
		return false;
	}
	//删除
	public static function delete($name){
		if($name){
			$model = Y_Db::init('seos');
			return $model->where(array('name'=>$name))->delete();
		}
		return false;
	}
	//获取所有
	public static function getInfo($name){
		if($name){
			$model = Y_Db::init('seos');
			return $model->where(array('name'=>$name))->find();
		}
		return false;
	}
}
?>
