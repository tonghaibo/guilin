<?php
/**
 * 酒店房间操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Hotel_Rooms{
	//上传插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('hotel_rooms');
			return $model->insert($data);
		}
		return false;
	}
	//更新
	public static function update($id,$data){
		if($id and $data){
			$model = Y_Db::init('hotel_rooms');
			return $model->where(array('rid'=>$id))->update($data);
		}
		return false;
	}
	//获取所有
	public static function getInfo($id){
		if($id){
			$model = Y_Db::init('hotel_rooms');
			return $model->find($id);
		}
		return false;
	}
	//获取列表
	public static function getList($hid,$flag=false){
		if($hid){
			$model = Y_Db::init('hotel_rooms');
			$where['hid'] = $hid;
			if($flag){
				$where['status']=1;
				$where['isfull'] = 0;
			}
			$model->where(array('hid'=>$hid));
			return $model->select();
		}
		return false;
	}
	//获取总数
	public static function getCount($hid){
		if($hid){
			$model = Y_Db::init('hotel_rooms');
			$model->where(array('hid'=>$hid));
			return $model->count();
		}
		return 0;
	}
	//获取基本
	public static function getBase($id){
		if($id){
			$model = Y_Db::init('hotel_rooms');
			$model->field(array('name','img','price'));
			return $model->find($id);
		}
		return false;
	}
}
?>
