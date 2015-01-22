<?php
/**
 * 用户等级积分记录表管理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-14
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_User_Ranklog{
	public static function insert($data){
		if(empty($data)) return false;
		$model = Y_Db::init('user_ranklog');
		return $model->insert($data);
	}
	//获取列表
	public static function getList($uid=0){
		$model = Y_Db::init('user_ranklog');
		if($uid){
			$model->where(array('uid'=>$uid));
		}else{
			$model->where('true');
		}
		return $model->select();
	}
	//获取用户日限制
	public static function getRank($uid,$pid){
		$model = Y_Db::init('user_ranklog');
		$time = strtotime(date('Y-m-d'));
		$model->where(array('uid'=>$uid,'pid'=>$pid,'dotime>'=>$time));
		$num = $model->field('sum(rank) as num')->find();
		if($num){
			return $num['num'];
		}
		return 0;

	}
	//记录积分 
	public static function setLog($uid,$pid){
		$rank = Ly_User_Rankpower::getInfo($pid);
		$user = Y_Db::init('user');
		$u = $user->field('rank')->find($uid);
		if(!$user) return false;		
		if(!$rank) return false;
		$log['uid'] = $uid;
		$log['pid'] = $pid;
		$log['dotime'] = time();
		$log['gid'] = 0;
		if($rank['dayrank']!=0){
			//判断是否超过日限制
			$num = self::getRank($uid,$pid);
			if($num<$rank['dayrank']){
				if($rank['type']==1){
					$rank['rank'] = 0-$rank['rank'];
				}		
			}else{
				//不加直接返回
				return false;
			}
		}
		$log['afterrank'] = $u['rank']+$rank['rank'];
		$log['action'] = $rank['name'];
		$log['rank'] = $rank['rank'];
		//写入日志
		self::insert($log);
		//更新用户表
		//获取用户目前等级
		$ranklevel = Ly_User_Rank::getRid($log['afterrank'])+0;
		$user->where(array('uid'=>$uid))->update(array('rank'=>$log['afterrank'],'ranklevel'=>$ranklevel));
		return true;	
	}
}
?>
