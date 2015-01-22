<?php
/**
 * 消息表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Message{
	//上传插入
	public static function insert($data){
		if($data){
			$data['messtime'] = time();
			$model = Y_Db::init('message');
			return $model->insert($data);
		}
		return false;
	}
	//删除
	public static function delete($id){
		if($id){
			$model = Y_Db::init('message');
			return $model->where(array('id'=>$id))->delete();
		}
		return false;
	}
	public static function update($id,$data){
		if($id){
			$model = Y_Db::init('message');
			return $model->where(array('id'=>$id))->update($data);
		}
		return false;
	}
	//获取所有
	public static function getInfo($id){
		if($id){
			$model = Y_Db::init('message');
			return $model->where(array('id'=>$id))->find();
		}
		return false;
	}
	//获取最近通讯用户
	public static function getMylistuid($uid,$flag=true){
		if($uid){
			if($flag){
				$where = array('fromuid'=>$uid);
				$group = 'touid';
			}else{
				$where = array('touid'=>$uid);
				$group = 'fromuid';
			}
			//取一个月的
			$where['messtime>'] = time()-25920000;
			$model = Y_Db::init('message');
			return $model->field(array('id','fromuid','touid','messtime'))->where($where)->order('id DESC')->group($group)->select();
		}
		return false;
	}
	//发送消息 $t=0评论 1为回复
	public static function sendComments($uid,$touid,$mess,$t=0){
		if($uid && $touid  && $mess){
			if($uid==$touid) return false;
			//获取是否可以发送
			$model = Y_Db::init('user_messset');
			$c['c'] = 1;
			$c['r'] = 1;
			if($d=$model->field(array('comments','replay'))->find($touid)){
				$c['c'] = $d['comments'];
				$c['r'] = $d['replay'];
			}
			$data['fromuid'] = $uid;
			$data['touid'] = $touid;
			$data['messages'] = $mess;
			if($t==0 && $c['c']){
				//评论
				self::insert($data);
				return true;
			}else if($c['r']){
				//回复评论
				self::insert($data);
				return true;
			}
		}
		return false;
	}
	//发送信件
	public static function send($fuid,$content,$uid){
		if($fuid && $content){
			$uid = $uid?$uid:Y_Session::get('uid');
			$rank = Ly_User::getRanklevel($uid);
			$num = Ly_User_Rank::getNum($rank,'message');
			$model = Y_Db::init('message');
			if($num!=0){
				$time = strtotime("-1 days");
				//获取自己的日发量
				$m = $model->where(array('fuid'=>$uid,'messtime>'=>$time))->count()+0;
				if($m>=$num) return 1;
			}
			//获取对方设置
			$messset = Ly_User_Messset::getInfo($fuid);
			if($messset['mess']==0){
				//我关注的人
				if(!Ly_Friend::isFriend($uid,$fuid)) return 3;
			}else if($messset['mess']==1){
				//关注我的人和我关注的人
				if(!Ly_Friend::isFriend($fuid,$uid) && !Ly_Friend::isFriend($uid,$fuid)) return 4;
			}
			$model->insert(array('fromuid'=>$uid,'touid'=>$fuid,'messages'=>$content,'messtime'=>time()));
			//通知
			$model = Y_Db::init('user');
			$model->where($fuid)->update("message=message+1");
			return 2;
		}
		return false;
	}
}
?>
