<?php
/**
 * 标签库绑定
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-10-12
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Tags_Bind{
	//插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('tags_bind');
			return $model->insert($data);
		}
		return false;
	}
	//获取列表
	public static function getList($data){
		$model = Y_Db::init('tags_bind');
		return $model->where($data)->select();
	}
	//获取总数
	public static function getCount($data){
		$model = Y_Db::init('tags_bind');
		return $model->where($data)->count();
	}
	//删除
	public static function delete($data){
		if($data){
			$model = Y_Db::init('tags_bind');
			return $model->where($data)->delete();
		}
		return false;
	}
	//获取标签列表
	public static function getTags($data,$flag=false){
		if($data){
			$model = Y_Db::init('tags_bind');
			if($flag){
				$model->field('group_concat(tid) as t');
				$f = $model->where($data)->find();
				return $f?$f['t']:'';
			}
			$list = $model->where($data)->select();
			if($list){
				foreach($list as $v){
					$i['id'] = $v['tid'];
					$i['name'] = Ly_Tags::getName($v['tid']);
					$datas[] = $i;
				}
				return $datas;
			}
		}
		return false;
	}
	//获取对应标签类型id
	public static function getIds($ids,$type='c',$flag=false){
		$type = strtolower($type);
		$arr = array('c','w','h','u');
		if(in_array($type,$arr)){
			$model = Y_Db::init('tags_bind');
			$model->where("tid in($ids)");
			$id = $type.'id';
			if($flag){
				$i = $model->field("group_concat($id) as i")->group($id)->select();
				return $i?$i['i']:'';
			}else{
				return $model->field($id)->group($id)->select();
			}
		}
		return false;
	}
}
?>
