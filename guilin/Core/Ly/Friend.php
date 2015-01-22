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
class Ly_Friend{
	//上传插入
	public static function insert($data){
		if($data){
			$data['pubtime'] = time();
			$model = Y_Db::init('friend');
			return $model->insert($data);
		}
		return false;
	}
	//删除
	public static function delete($uid,$fid){
		if($uid){
			$model = Y_Db::init('friend');
			return $model->where(array('uid'=>$uid,'fuid'=>$fid))->delete();
		}
		return false;
	}
	public static function update($id,$fid,$data){
		if($id){
			$model = Y_Db::init('friend');
			return $model->where(array('uid'=>$id,'fuid'=>$fid))->update($data);
		}
		return false;
	}
	//获取好友和关注我的人
	public static function getList($uid,$flag=true){
		if($uid){
			$model = Y_Db::init('friend');
			if($flag){
				//我关注的人
				$where['uid'] = $uid;
				$group = 'fuid';
			}else{
				//关注我的人
				$where['fuid'] = $uid;
				$group = 'uid';
			}
			return $model->field(array('uid','fuid','pubtime','name'))->where($where)->order('pubtime desc')->group($group)->select();
		}
		return false;
	}
	//是否已经关注
	public static function isFriend($uid,$my=''){
		$my = $my?$my:Y_Session::get('uid');
		if($uid){
			$model = Y_Db::init('friend');
			$model->where(array('uid'=>$my,'fuid'=>$uid));
			$model->field('pubtime');
			if($model->find()) return true;
		}
		return false;
	}
	//添加关注
	public static function addFriend($uid,$name=''){
		if($uid){
			//已经关注
			if(self::isFriend($uid)) return 1;
			$my = Y_Session::get('uid');
			$model = Y_Db::init('friend');
			$f = $model->where(array('uid'=>$my))->count();
			$rank = Ly_User::getRanklevel($my);
			$num = Ly_User_Rank::getNum($rank,'attention');
			//好友数超出
			if($num!=0 && $f>=$num) return 2;
			$f1 = $model->where(array('uid'=>$uid))->count();
			$rank = Ly_User::getRanklevel($uid);
			$num = Ly_User_Rank::getNum($rank,'follow');
			//好友数超出
			if($num!=0 && $f1>=$num) return 3;
			$model->insert(array('uid'=>$my,'fuid'=>$uid,'pubtime'=>time(),'name'=>$name));
			//好友数
			$model = Y_Db::init('user');
			$model->where($uid)->update(array('follow'=>($f1+1)));
			$model->where($my)->update(array('attention'=>($f+1)));
			//加分
			Ly_User_Ranklog::setLog($my,3);
			//是否通知
			if($uid!=$my){
				$d['uid'] = $uid;
				$u = Ly_User::getName($my);
				$d['notice'] = '<a href="user.html?uid='.$my.'" target="_blank">'.$u.'</a>：我已经加你为好友了！';
				$d['title'] = '您有一位新粉丝';
				$d['url'] = 'user.html?uid='.$my;
				$d['status'] = 1;
				Ly_Notice::insert($d);
			}
			return 4;
		}
		return false;
	}
	//取消关注
	public static function delFriend($uid){
		if($uid){
			$my = Y_Session::get('uid');
			if($my){
				$model = Y_Db::init('friend');
				if($model->where(array('uid'=>$my,'fuid'=>$uid))->delete()){
					$model = Y_Db::init('user');
					$model->where(array('uid'=>$uid))->update('follow=follow-1');
					$model->where(array('uid'=>$my))->update('attention=attention-1');
					Ly_User_Ranklog::setLog($my,17);
					return true;
				}
			}
		}
		return false;
	}
}
?>
