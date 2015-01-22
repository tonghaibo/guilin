<?php
/**
 * cookie操作类
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-23
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if (!defined('IN_Y')) {
	exit();
}
class Y_Cookie{
	public static $prefix;
	private static function init(){
		if(!self::$prefix){
			$sc = Y::Config('sys','sc');
			self::$prefix = $sc;
		}
		
	}
	/**
	 * 判断cookie变量是否存在
	 * 
	 * @access public
	 * @param string $cookie_name	cookie的变量名
	 * @return boolean
	 */
	public static function is_set($cookie_name) {
		
		if (!$cookie_name) {
			return false;
		}
		self::init();
		return isset($_COOKIE[self::$prefix.$cookie_name]);
	}
	
	/**
	 * 获取某cookie变量的值
	 * 
	 * 获取的数值是进过64decode解密的,注:参数支持数组
	 * @access public
	 * @param string $cookie_name	cookie变量名
	 * @return string
	 */
	public static function get($cookie_name) {
		if (!$cookie_name) {
			return false;
		}
		self::init();
		return isset($_COOKIE[self::$prefix.$cookie_name]) ? unserialize(base64_decode($_COOKIE[self::$prefix.$cookie_name])) : false;
	}
	/**
	 * 设置某cookie变量的值
	 * 
	 * 注:这里设置的cookie值是经过64code加密过的,要想获取需要解密.参数支持数组
	 * @access public
	 * @param string $name 		cookie的变量名
	 * @param string $value 	cookie值
	 * @param intger $expire	cookie所持续的有效时间,默认为一小时.(这个参数是时间段不是时间点,参数为一小时就是指从现在开始一小时内有效)
	 * @param string $path		cookie所存放的目录,默认为网站根目录
	 * @param string $domain	cookie所支持的域名,默认为空
	 * @return void	
	 */
	public static function set($name, $value, $expire = null, $path = null, $domain = null) {
		//参数分析.
		if(is_null($expire)){
			$expire = time()+(Y::Config('sys','days'))*86400;
		}else{
			$expire = time()+86400*$expire;
		}
		self::init();
		//$expire = is_null($expire) ? time()+3600 : time()+$expire;
		if (is_null($path)) {
			$path = '/';
		}
		//数据加密处理.	
		$value = base64_encode(serialize($value));
		setcookie(self::$prefix.$name, $value, $expire, $path, $domain);
		$_COOKIE[self::$prefix.$name] = $value;		
	}
	
	/**
	 * 删除某个Cookie值
	 * 
	 * @access public
	 * @param string $name	cookie的变量值
	 * @return void
	 */
	public static function delete($name) {
		self::init();
		self::set($name, '', '-3600');
		unset($_COOKIE[self::$prefix.$name]);
	}	
	/**
	 * 清空cookie
	 * 
	 * @access public
	 * @return void
	 */
	public static function clear() {
		unset($_COOKIE);
	}
}
