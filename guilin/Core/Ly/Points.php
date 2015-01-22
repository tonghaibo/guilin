<?php
/**
 * 后台节点控制表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-06
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Points{
	//变量保存
	public static $control;
	public static $action;
	//获取顶部所有标签
	public static function getToplist(){
		$model = Y_Db::init('points');
		$model->where(array('fid'=>0,'isview'=>1));
		$model->field(array('name','control','action'));
		$model->order('sort asc,id asc');
		return $model->select();
	}
	//获取所有的子标签
	public static function getSublist(){
		$model = Y_Db::init('points');
		$where = "control='".Y::$controller."' AND level=2 AND isview=1";
		$model->where($where);
		$model->field(array('name','action','method'));
		$model->order('sort asc,id asc');
		return $model->select();
	}
	//获取所有的子子标签
	public static function getSubsublist(){
		$model = Y_Db::init('points');
		$where = "control='".Y::$controller."' AND action='".Y::$action."' AND level=3 AND isview=1";
		$model->where($where);
		$model->field(array('name','action','method'));
		$model->order('sort asc,id asc');
		return $model->select();
	}
	//获取当前节点信息
	public static function getBase($id){
		if(empty($id)) return false;
		$model = Y_Db::init('points');
		$model->field(array('name','des'));
		return $model->find($id);
	}
	//获取对应控制器和方法的ID
	public static function getId(){
		$data['control'] = Y::$controller;
		$data['action'] = Y::$action;
		$data['method'] = isset($_GET['method'])?$_GET['method']:'';
		$where = "control='{$data['control']}'";
		if($data['action']){
			$where .= " AND `action`='{$data['action']}'";
		}else{
			$where .= " AND fid=0";
		}
		if($data['method']){
			$where .= " AND method='{$data['method']}'";
		}else{
			$where .= " AND (method is NULL or method='')";
		}
		$model = Y_Db::init('points');
		$model->where($where);
		$model->field(array('id','ispublic','name','des'));
		$info = $model->find();
		if($info){
			return $info;
		}
		return false;
	}	
	//无限极分类
	public static function getList(){
		$model = Y_Db::init('points');
		$sql = "SELECT id,name,concat(path,'-',id) as bpath from ".Y::Config('db','prefix').'points order by bpath';
		return $model->query($sql,'select');
	}
	public static function getLists($fid=0){
		$model = Y_Db::init('points');
		$model->where(array('fid'=>$fid));
		$model->field(array('id','name'));
		return $model->select();
	}
	//添加
	public static function insert($data){
		if(empty($data)) return false;
		$model = Y_Db::init('points');
		$id = $model->insert($data);
		if($id){
			$model->where(array('id'=>$data['fid']))->field('path');
			$path = $model->find();
			if($path){
				$up['path'] = $path['path'].'-'.$data['fid'];
				$up['level'] = count(explode('-',$path['path']))+1;
			}else{
				$up['path'] = 0;
				$up['level'] = 1;
			}
			$model->where(array('id'=>$id))->update($up);
			return true;
		}else{
			return false;
		}
	}
	//更新
	public static function update($data,$id){
		if(empty($id) or empty($data)) return false;
		$model = Y_Db::init('points');
		$model->where(array('id'=>$id));
		if($model->update($data)){
			//更新path
			$model->where(array('id'=>$data['fid']))->field('path');
			$path = $model->find();
			if($path){
				$up['level'] = count(explode('-',$path['path']))+1;
				$up['path'] = $path['path'].'-'.$data['fid'];
			}else{
				$up['path'] = 0;
				$up['level'] = 1;
			}
			$model->where(array('id'=>$id))->update($up);
			return true;
		}else{
			return false;
		}
	}
	//获取信息
	public static function info($id){
		if(empty($id)) return false;
		$model = Y_Db::init('points');
		$model->where(array('id'=>$id));
		$model->field(array('id','fid','name','control','action','method','sort','des','ispublic','isview'));
		return $model->find();
	}
	//删除
	public static function delete($id){
		if(empty($id)) return false;
		$model = Y_Db::init('points');
		$model->where(array('id'=>$id));
		return $model->delete();
	}
	//获取名称
	public static function getName($id){
		if(empty($id)) return false;
		$model = Y_Db::init('points');
		$model->field('name');
		if($data=$model->find($id)){
			return $data['name'];
		}
		return false;
	}
}
?>
