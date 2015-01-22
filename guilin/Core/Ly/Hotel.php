<?php
/**
 * 酒店操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Hotel{
	//上传插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('hotel');
			return $model->insert($data);
		}
		return false;
	}
	//更新
	public static function update($id,$data){
		if($id and $data){
			$model = Y_Db::init('hotel');
			return $model->where(array('hid'=>$id))->update($data);
		}
		return false;
	}
	//获取所有
	public static function getInfo($id){
		if($id){
			$model = Y_Db::init('hotel');
			return $model->find($id);
		}
		return false;
	}
	//获取基本的
	public static function getBase($id){
		if($id){
			$model = Y_Db::init('hotel');
			$model->field(array('name','img','uid','contact','classes','star'));
			return $model->find($id);
		}
		return false;
	}
	//获取名字
	public static function getName($id){
		if($id){
			$model = Y_Db::init('hotel');
			$name = $model->field(array('name'))->find($id);
			if($name){
				return $name['name'];
			}
		}
		return false;
	}
	//获取周边酒店
	public static function getNearby($lng,$lat,$num=10){
		
	}
	//标签推荐
	public static function getBinds($tid,$id=null,$num=10){
		if($tid){
			$model = Y_Db::init('tags_bind');
			$where = '';
			if($id && is_numeric($id)){
				$where = "z.hid!={$id} AND ";
			}
			$where .= "z.hid!=0 AND z.cid=0 AND z.wid=0 AND l1.status=1 AND l1.img!='' AND z.tid in($tid)";
			$model->where($where);
			$model->field(array('hid'));
			$hotel = Y_Db::init('hotel');
			$hotel->field(array('name','img','comment','price'));
			$hotel->where("l1.hid=z.hid");
			$model->group('z.hid');
			$model->limit($num);
			return $model->join($hotel);
		}
		return false;
	}
}
?>
