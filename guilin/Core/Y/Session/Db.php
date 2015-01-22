<?php
/**
 * Y框架Session数据库保存
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-10-25
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Y_Session_Db{
	private static $model=null;
	private static $maxtime=null;
	private static $nowtime;
	private static $client_ip;
	//初始化程序
	private static function init(){
		//
		self::$maxtime = ini_get('session.gc_maxlifetime');
		self::$client_ip = $_SERVER['REMOTE_ADDR']?htmlspecialchars($_SERVER['REMOTE_ADDR']):'unknown';
		self::$nowtime= time();
	}
	public static function start($tab){
		self::init();
		session_set_save_handler(
			array(__CLASS__,'open'),
			array(__CLASS__,'close'),
			array(__CLASS__,'read'),
			array(__CLASS__,'write'),
			array(__CLASS__,'destroy'),
			array(__CLASS__,'gc')
		);
		self::$model = Y_Db::init($tab);
		session_start();
	}
	public static function open($path,$name){
		return true;
	}
	public static function close(){
		return true;
	}
	public static function read($sid){
		$data = self::$model->find($sid);
		if($data){
			//时间是否过期
			if(($data['uptime']+self::$maxtime)<self::$nowtime){
				//删除
				self::$model->where(array('sid'=>$sid))->delete();
				return '';
			}
			//是否IP相等
			if($data['client_ip']!=self::$client_ip){
				self::$model->where(array('sid'=>$sid))->delete();
				return '';
			}
			return $data['data'];
		}
		return '';
	}
	//写
	public static function write($sid,$data){
		$info = self::$model->find($sid);
		
		if($info){
			//是否相等，或者防止刷新
			if($info['data']!=$data || self::$nowtime>($info['uptime']+30)){
				$db['uptime']  = self::$nowtime;
				$db['data'] = $data;
				self::$model->where(array('sid'=>$sid))->update($db);
			}
		}else{
			$db['sid'] = $sid;
			$db['client_ip'] = self::$client_ip;
			$db['uptime']  = self::$nowtime;
			$db['data'] = $data;
			self::$model->insert($db);
		}
		return true;
	}
	//销毁
	public static function destroy($sid){
		self::$model->where(array('sid'=>$sid))->delete();
		return true;
	}
	//系统销毁
	private static function gc(){
		$time = self::$nowtime-self::$maxtime;
		self::$model->where(array('uptime<'=>$time))->delete();
		return true;
	}
}
?>
