<?php
/**
 * 线路天数操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Wayline_Days{
	//上传插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('wayline_days');
			return $model->insert($data);
		}
		return false;
	}
	//更新
	public static function update($wid,$day,$data){
		if($wid and $data and $day){
			$model = Y_Db::init('wayline_days');
			return $model->where(array('wid'=>$wid,'daynum'=>$day))->update($data);
		}
		return false;
	}
	//获取所有
	public static function getInfo($wid,$day){
		if($wid && $day){
			$model = Y_Db::init('wayline_days');
			$model->where(array('wid'=>$wid,'daynum'=>$day));
			return $model->find();
		}
		return false;
	}
	//删除多余
	public static function delete($wid,$day,$flag=false){
		if($wid && $day){
			$model = Y_Db::init('wayline_days');
			if($flag){
				$model->where(array('wid'=>$wid,'daynum>'=>$day+1));
			}else{
				$model->where(array('wid'=>$wid,'daynum'=>$day));
			}
			return $model->delete();
		}
		return false;
	}
}
?>
