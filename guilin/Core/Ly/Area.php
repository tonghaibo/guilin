<?php
/**
 * 地区
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Ly_Area{
	//获取列表
	public static function getList($upid=0){
		$model = Y_Db::init('area');
		$where['upid'] = intval($upid);
		$model->where($where);
		$model->order('sort DESC,id asc');
		$model->field(array('id','name','upid','sort'));
		return $model->select();
	}
	//获取全部地址
	public static function getAddress($id){
		$model = Y_Db::init('area');
		$model->field(array('id','name','upid'));
		$info = $model->find($id);
		$str = '';
		if($info){
			if($info['upid']!=0){
				$str .= self::getAddress($info['upid']);
			}
			$str  .= $info['name'];
		}
		return $str;
	}
	//更新
	public static function update($id,$data){
		if($data && $id){
			$model = Y_Db::init('area');
			return $model->where(array('id'=>$id))->update($data);
		}
		return false;

	}
	//插入
	public static function insert($data){
		if(is_array($data)){
			$model = Y_Db::init('area');
			return $model->insert($data);
		}
		return false;
	}
	//获取名称
	public static function getName($id){
		if($id){
			$model = Y_Db::init('area');
			$model->field('name');
			$d = $model->find($id);
			if($d){
				return $d['name'];
			}
		}
		return false;
	}
}
?>
