<?php
/**
 * 标签库添加
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-10-12
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Tags{
	//插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('tags');
			return $model->insert($data);
		}
		return false;
	}
	//更新
	public static function update($id,$data){
		if($id and $data){
			$model = Y_Db::init('tags');
			return $model->where(array('id'=>$id))->update($data);
		}
		return false;
	}
	//获取列表
	public static function getList($pid=0,$num=0,$max=100){
		$model = Y_Db::init('tags');
		return $model->where(array('pid'=>$pid))->order('sort DESC,id DESC')->limit($num,$max)->select();
	}
	//获取总数
	public static function getCount($pid=0){
		$model = Y_Db::init('tags');
		return $model->where(array('pid'=>$pid))->count();
	}
	//删除
	public static function delete($id){
		if($id){
			$model = Y_Db::init('tags');
			return $model->delete($id);
		}
		return false;
	}
	//获取信
	public static function getInfo($id){
		if($id){
			$model = Y_Db::init('tags');
			return $model->find($id);
		}
		return false;
	}
	//获取名字
	public static function getName($id){
		if($id){
			$model = Y_Db::init('tags');
			$name = $model->field(array('name'))->find($id);
			if($name){
				return $name['name'];
			}
		}
		return false;
	}
	public static function getAll($pid){
		$model = Y_Db::init('tags');
		return $model->where(array('pid'=>$pid))->select();
	}
	//获取热门标签
	public static function getHot($num=10,$type=0){
		$model = Y_Db::init('tags');
		$model->field(array('id','name'));
		$num = $num+'1';
		$where = 'true';
		if($type!=0){
			$where .= "  AND pid={$type}";
		}else{
			$where .= " AND pid!=0";
		}
		$model->where($where)->order('sort DESC,id DESC')->limit($num);
		return $model->select();
	}
}
?>
