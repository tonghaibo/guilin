<?php
/**
 * 管理员表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-04
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Group{
	protected static $model;
	protected static function init(){
		if(!self::$model){
			self::$model = Y_Db::init('group');
		}
		return self::$model;
	}
	//查找组权限
	public static function getGpower($gid,$uid){
		if(empty($gid) or empty($uid)) return false;
		$model = self::init();
		$model->where(array('gid'=>$gid));
		$model->field(array('leaderid','power','members','issuper'));
		$res = $model->find();
		if($res){
			if($res['leaderid']==$uid || in_array($uid,explode(',',$res['members']))){
				if($res['issuper']){
					$data['issuper'] = 1;
				}
				$data['power'] = explode(',',$res['power']);
				return $data;
			}else{
				return false;
			}
		}
		return false;
	}
	//获取列表
	public static function getList(){
		$model = Y_Db::init('group');
		return $model->field(array('gid','gname','leaderid'))->select();
	}
	//插入
	public static function insert($data){
		if(empty($data)) return false;
		$model = Y_Db::init('group');
		return $model->insert($data);
	}
	//获取信息
	public static function getInfo($gid){
		if(empty($gid)) return false;
		$model = Y_Db::init('group');
		return $model->find($gid);
	}
	//update
	public static function update($gid,$data){
		if(empty($gid)) return false;
		$model = Y_Db::init('group');
		return $model->where(array('gid'=>$gid))->update($data);
	}
	//获取组名
	public static function getGname($gid){
		if(empty($gid)) return false;
		$model = Y_Db::init('group');
		$model->field('gname');
		$info = $model->find($gid);
		if($info){
			return $info['gname'];
		}
		return false;
	}
	//获取flag状态
	public static function getFlag($flag){
		if($flag==0){
			return '是';
		}else{
			return '否';
		}
	}
	//更新组员
	public static function upMembers($gid,$uid){
		if(empty($gid) or empty($uid)) return false;
		$model = Y_Db::init('group');
		$model->field('members');
		if($info=$model->find($gid)){
			$uid = intval($uid);
			$info = explode(',',$info['members']);
			array_push($info,$uid);
			$info = array_unique($info);
			$info = trim(join(',',$info),',');
			if($model->where(array('gid'=>$gid))->update(array('members'=>$info))){
				return true;
			}
			return false;
			
		}
		return false;
	}
}
?>
