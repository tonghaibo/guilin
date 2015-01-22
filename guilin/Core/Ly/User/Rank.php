<?php
/**
 * 用户等级表管理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-14
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User_Rank{
	public static function insert($data){
		if(empty($data)) return false;
		$model = Y_Db::init('user_rank');
		return $model->insert($data);
	}
	public static function update($rid,$data){
		if(!$rid or empty($data)) return false;
		$model = Y_Db::init('user_rank');
		return $model->where(array('rid'=>$rid))->update($data);
	}
	//获取列表
	public static function getList(){
		$model = Y_Db::init('user_rank');
		return $model->select();
	}
	//获取最大
	public static function getMax(){
		$model = Y_Db::init('user_rank');
		return $model->order('rid DESC')->find();
	}
	//获取值
	public static function getRank($id){
		if(empty($id)) return false;
		$model = Y_Db::init('user_rank');
		$rank = $model->field('rank')->find($id);
		if($rank){
			return $rank['rank'];
		}else{
			return false;
		}
	}
	//根据等级分数获取等级ID
	public static function getRid($rank){
		if($rank){
			$rank = intval($rank);
			$model = Y_Db::init('user_rank');
			$model->field('rid');
			$model->where(array('rank>'=>$rank));
			$model->order('rank ASC');
			$info = $model->find();
			if($info){
				return $info['rid'];
			}
		}
		return false;	
	}
	//获取等级名称
	public static function getName($id){
		if(empty($id)) return false;
		$model = Y_Db::init('user_rank');
		$rank = $model->field('name')->find($id);
		if($rank){
			return $rank['name'];
		}else{
			return false;
		}
	}
	//获取限制值$name=share,follow,attention,collect,message,visits
	public static function getNum($id,$name='share'){
		if($id){
			$model = Y_Db::init('user_rank');
			$model->where($id);
			$model->field($name);
			$num = $model->find();
			if($num){
				return $num[$name];
			}
		}
		return false;
	}
}
?>
