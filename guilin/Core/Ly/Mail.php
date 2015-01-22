<?php
/**
 * 游桂林网邮件发送
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-04
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Ly_Mail{
	protected static $from = 'yuexinok@126.com';
	public static function send($to,$from='',$type=1){
		if(empty($to)) return false;
		if((time()-Y_Cookie::get('emailtime'))<60){
			return 4;
		}
		$user = Y_Db::init('user');
		$user->where(array('email'=>$to))->field(array('uid'));
		$uid = $user->find();
		if(empty($uid)){
			return 2;
		}
		$from = $from?$from:self::$from;
		$model = Y_Db::init('mailtpl');
		$type = intval($type);
		$model->where(array('flag'=>1,'tpltype'=>$type));
		$model->field(array('subject','content'));
		$res = $model->find();
		if($res){
			$rand = substr(md5(mt_rand()),5,8);
			$url = Y::Config('sys','domain');
			if($type==1){
				$url .= 'index_emailact.html?';
			}elseif($type=='2'){
				$url .= 'index_setpass.html?';
			}elseif($type==4){
				$url .= 'index_emailact.html?t=4';
			}
			$url .= '&email='.$to.'&token='.$rand.'&id='.$uid['uid'];
			$res['content'] = preg_replace('/{url}/',$url,$res['content']);
			$res['content'] = preg_replace('/{hostmail}/',$from,$res['content']);
			$res['content'] = preg_replace('/{email}/',$to,$res['content']);
			$res['content'] = preg_replace('/{host}/',Y::Config('sys','domain'),$res['content']);
			if(Y_Mail::send($to,$from,$res['subject'],$res['content'])){
				$user->where(array('uid'=>$uid['uid']))->update(array('rndnum'=>$rand));
				//cookie设置
				Y_Cookie::set('emailtime',time());
				return 0;
			}else{
				return 3;
			}
		}
		return 1;
	}
	//检测地址是否有效并且激活邮箱认证
	public static function check($data=array()){
		if(empty($data)) return false;
		$user = Y_Db::init('user');
		$user->where(array('uid'=>$data['uid']));
		$user->field(array('email','rndnum'));
		$res = $user->find();
		//检测值是否相等
		if($res['email']==$data['email'] && $res['rndnum']==$data['token']){
			//更新邮箱地址
			$user->where(array('uid'=>$data['uid']));
			$user->update(array('rzemail'=>1,'rndnum'=>substr(md5(mt_rand()),5,8)));
			return true;
		}else{
			return false;
		}
	}
	//插入
	public function insert($data){
		if($data){
			$model=Y_Db::init('mailtpl');
			return $model->insert($data);
		}
		return false;
	}
	//根新
	public function update($id,$data){
		if($data and $id){
			$model=Y_Db::init('mailtpl');
			$model->where(array('tplid'=>$id));
			return $model->update($data);
		}
		return false;
	}
	//信息
	public function getInfo($id){
		if($id){
			$model=Y_Db::init('mailtpl');
			return $model->find($id);
		}
		return false;
	}
}
?>
