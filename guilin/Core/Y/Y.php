<?php
/**
 * Y框架主文件
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
ob_start();
ob_implicit_flush(0);
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set('PRC');
//定义权限
if(!defined('IN_Y')){
	exit('NO access Y!');
}
//定义框架位置
define('Y_DIR',rtrim(Y_ROOT,'/').'/');
//定义核心代码包含目录即Y框架的上级目录 Core
define('C_DIR',dirname(Y_DIR).'/');
//定义app目录
define('A_DIR',rtrim(APP,'/').'/');
//定义项目根目录 project
define('P_DIR',dirname(A_DIR).'/');
//定义服务器
//$host = '/'.substr(dirname(__FILE__),strlen($_SERVER['DOCUMENT_ROOT']));

//define('Y_HOST_DIR',substr($host,0,-strlen(Y_DIR)).'/');
//define('Y_HOST',$_SERVER['HTTP_HOST'].'/');
//unset($host);
//定义默认执行的control
if(!defined('DEFAULT_C')){
	define('DEFAULT_C','index');
}
if(!defined('DEFAULT_A')){
	//定义默认执行的动作
	define('DEFAULT_A','index');
}
//关闭魔术变量，提高PHP运行效率.
if (get_magic_quotes_runtime()) {	
	set_magic_quotes_runtime(0);
}
define('Y_MAGIC',get_magic_quotes_gpc()?false:true);
class Y{
	//执行的Controller
	public static $controller;
	//执行的动作
	public static $action;
	public static $startTime;
	//载入的文件
	public static $_inc_files = array();
	//配置文件
	public static $config = array();
	//语言文件
	public static $lang = array();
	//url分析
	private static function parseUrl(){
		self::$controller = isset($_GET['ro'])?strtolower($_GET['ro']):DEFAULT_C;
		self::$action = isset($_GET['ac'])?strtolower($_GET['ac']):DEFAULT_A;
		return true;
	}
	//项目运行函数
	public static function run(){
		self::$startTime = microtime(true);		
		if(defined('Y_DEBUG') && Y_DEBUG==true){
			error_reporting(E_ALL ^ E_NOTICE);
			//ini_set('display_errors','Off');
			include Y_DIR.'Debug.php';
			set_error_handler(array('Y_Debug','handler'));
			Y_Debug::start();
		}else{
			//ini_set('log_errors', 'On');
			error_reporting(0);
			include Y_DIR.'Log.php';
			set_error_handler(array('Y_Log','handler'));
		}
		//开启session
		Y_Session::init(defined('Y_SESSION')?Y_SESSION:'',defined('Y_SESSION_TAB')?Y_SESSION_TAB:'session');
		//执行解析
		self::parseUrl();
		//自动创建目录
		Y_Structure::runtime();
		//是否已经缓存
		Y_Cache::get();
		//执行
		if(is_file(A_DIR.'controllers/'.self::$controller.'Controller.php')){
			self::loadFile(A_DIR.'controllers/'.self::$controller.'Controller.php');
		}else{
			self::display404();
		}
		$controller = self::$controller.'Controller';
		$app_objec = new $controller();
		$action = self::$action.'Action';
		if(method_exists($controller,$action)){
			$app_objec->$action();
		}else{
			self::display404();
		}
	}
	//404页面错误提示
	private function display404(){
		is_file(A_DIR . 'views/error/error404.html') ? self::loadFile(A_DIR. 'views/error/error404.html') : self::loadFile(Y_DIR. 'views/error404.html');
		exit;
	}
	//自动加载
	public static function autoLoad($name){
		$name = str_replace('_','/',$name);
		self::loadFile(C_DIR.$name.'.php');
	}
	//包含文件 被包含的则保存在变量中
	public static function loadFile($file){
		if(!$file) return false;
		if(isset(self::$_inc_files[$file])==false){
			if(file_exists($file)){
				include($file);
				self::$_inc_files[$file] = true;
				Y_Debug::addMsg($file,1);	
			}else{
				Y_Debug::addMsg('加载'.$file.'文件失败！');
			}
			
		}
		return self::$_inc_files[$file];
		
	}
	
	//加载配置文件
	public static function Config($name,$key=null){
		if(isset(self::$config[$name])){
			if($key){
				return isset(self::$config[$name][$key])?self::$config[$name][$key]:false;
			}else{
				return self::$config[$name];
			}
		}else{
			$dir = P_DIR.'config/'.$name.'.config.php';
			if(file_exists($dir)){
				self::$config[$name] = include($dir);
				self::$_inc_files[$dir] = true;
				Y_Debug::addMsg($dir,1);
				if($key){
					return self::$config[$name][$key];
				}else{
					return self::$config[$name];
				}
			}else{
				Y_Debug::addMsg('加载配置文件'.$dir.'失败!');	
			}	
		}
		
	}
	//读取语言可以都取总共
	public static function Lang($filename,$name=null){
		if(isset(self::$lang[$filename])){
			return self::$lang[$filename][$name];
		}else{
			$dir = P_DIR.'language/'.$filename.'.lang.php';
			if(file_exists($dir)){
				self::$lang[$filename] = include($dir);
				self::$_inc_files[$dir] = true;
				Y_Debug::addMsg($dir,1);
				return self::$lang[$filename][$name];
			}else{
				Y_Debug::addMsg('加载语言文件'.$dir.'失败!');	
			}	
		}
	}
	//ajax载入 url地址 obj元素标签
	public static function load($url,$obj){
		echo '<script type="text/javascript">';
		echo '$(\''.$obj.'\').load(\''.$url.'\');';
		echo '</script>';
	}
}
//spl注册__autoload()函数
spl_autoload_register(array('Y','autoLoad'));
//运行
Y::run();
//是否输出调试信息
if(defined('Y_DEBUG') && Y_DEBUG==true){
	Y_Debug::stop();
	Y_Debug::show();
}
ob_end_flush();
?>
