<?php
/**
 * Y框架Session驱动器
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-10-25
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Y_Session{
	public static $prefix = 'YouGL_';
	//初始化
	public static function init($driver=null,$tab=null){
		if($driver==null or $driver==''){
			//默认的开启就行
			session_start();
		}else{
			$str = 'Y_Session_'.$driver;
			$str::start($tab);
		}
	}
	//读取session
	public static function get($key){
		if (!isset($_SESSION[self::$prefix.$key])) {
			return false;
		}

		return $_SESSION[self::$prefix.$key];
	}
	//设置session
	public static function set($name,$value){
		return $_SESSION[self::$prefix.$name] = $value;
	}
	//删除session
	public static function del($key){
		if (!isset($_SESSION[self::$prefix.$key])){
			return false;
		}
		unset($_SESSION[self::$prefix.$key]);

		return true;
	}
	//清除所有
	public static function clear(){
		$_SESSION = array();
	}
	public static function destory() {		
		if (session_id()){
			unset($_SESSION);
        	session_destroy();		
		}
	}
}
?>
