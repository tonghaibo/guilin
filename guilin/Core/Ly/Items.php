<?php
/**
 * 系统选项表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-30
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Items{
	public static $model;
	private static function init(){
		if(!self::$model){
			self::$model = Y_Db::init('items');
		}
		return self::$model;
	}
	//获取列表数组
	//Ly_Items::getName('user','education');
	public static function getList($tab,$col){
		$model = self::init();
		$model->where(array('tabname'=>$tab,'colname'=>$col))->field(array('items'));
		$data = $model->find();
		if($data){
			$data = explode('|',$data['items']);
			foreach($data as $key=>$val){
				$val = explode('#',$val);
				$info[$key]['id'] = $val[0];
				$info[$key]['name'] = $val[1];
			}
			return $info;
		}
		return false;
	}
	//根据选项ID和表名，列获取对应的名称
	//Ly_Items::getName('user','education',2);
	public static function getName($tab,$col,$id=0){
		if(!$id) false;
		$model = self::init();
		$model->where(array('tabname'=>$tab,'colname'=>$col))->field(array('items'));
		$data = $model->find();
		if($data){
			$data = explode('|',$data['items']);
			foreach($data as $val){
				$val = explode('#',$val);
				if($val[0]==$id){
					return $val[1];
				}
			}
		}
		return false;
	}
	//增加
	public static function insert($data){
		if(empty($data)) return false;
		$model = Y_Db::init('items');
		return $model->insert($data);
	}
	//修改
	public static function update($id,$data){
		if(empty($data) or empty($id)) return false;
		$model = Y_Db::init('items');
		return $model->where(array('itemsid'=>$id))->update($data);
	}
	//获取信息
	public static function getInfo($id){
		if(empty($id)) return false;
		$model = Y_Db::init('items');
		return $model->find($id);
	}
	//获取选项
	public static function getItems($id){
		if(empty($id)) return false;
		$model = Y_Db::init('items');
		$model->field('items');
		if($i=$model->find($id)){
			return $i['items'];
		}
		return false;
	}	
}
?>
