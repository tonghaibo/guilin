<?php
/**
 * 线路操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Wayline{
	//上传插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('wayline');
			return $model->insert($data);
		}
		return false;
	}
	//更新
	public static function update($id,$data){
		if($id and $data){
			$model = Y_Db::init('wayline');
			return $model->where(array('wid'=>$id))->update($data);
		}
		return false;
	}
	//获取所有
	public static function getInfo($id){
		if($id){
			$model = Y_Db::init('wayline');
			return $model->find($id);
		}
		return false;
	}
	//获取名字
	public static function getName($id){
		if($id){
			$model = Y_Db::init('wayline');
			$name = $model->field(array('name'))->find($id);
			if($name){
				return $name['name'];
			}
		}
		return false;
	}
	//获取基本信息
	public static function getBase($id){
		if($id){
			$model = Y_Db::init('wayline');
			$name = $model->field(array('name','price','pricehalf','contact','uid','img','days','traffic'))->find($id);
			if($name){
				return $name;
			}
		}
		return false;
	}
	//获取热门推荐
	public static function getHots($num=10){
		$model = Y_Db::init('wayline');
		$model->field(array('wid','name','days','price'));
		$model->where(array('status'=>1));
		$model->limit($num);
		return $model->order('ishot desc,wid desc')->select();
	}
	//标签推荐
	public static function getBinds($tid,$id=null,$num=10){
		if($tid){
			$model = Y_Db::init('tags_bind');
			$where = '';
			if($id && is_numeric($id)){
				$where = "z.wid!={$id} AND ";
			}
			$where .= " z.wid!=0 AND z.cid=0 AND z.hid=0 AND l1.status=1 AND l1.img!='' AND z.tid in($tid)";
			$model->where($where);
			$model->field(array('wid'));
			$hotel = Y_Db::init('wayline');
			$hotel->field(array('wid','name','days','price'));
			$hotel->where("l1.wid=z.wid");
			$model->group('z.wid');
			$model->limit($num);
			return $model->join($hotel);
		}
		return false;
	}
}
?>
