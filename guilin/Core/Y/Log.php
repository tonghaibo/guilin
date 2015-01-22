<?php
/**
 * log日志记录函数
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')){
	exit();
}
class Y_Log{
	//定义一个目录
	private static $dir = 'runtime/logs/';
	//定义日志文件级别0,1,2,3
	private static $class = array('Notice','Warning','Error','Unkown');
	//日志写入传入记录消息 保存文件名和消息级别
	public static function write($message,$name,$level=2){
		if($level>3) $level=3;
		if(!$message or !$name) return false;
		if(is_array($message)){
			//是数组则用一个tab隔开
			$message = join("\t",$message);
		}
		$log_file_name = P_DIR.self::$dir.date('Ym',time()).'_'.$name.'.php';
		//大于2M的重命名2097152
		if(is_file($log_file_name) && filesize($log_file_name)>=2097152){
			rename($log_file_name,P_DIR.self::$dir.date('Ymd',time()).'_'.$name.'.php');
		}
		$ip = $_SERVER['REMOTE_ADDR']?$_SERVER['REMOTE_ADDR']:'Unknown';
		$str = '<?php exit();?>'."\t".self::$class[$level]."\t".time()."\t".$message."\t".$ip;
		//写入日志
		error_log($str."\r\n",3,$log_file_name);
	}
	//日志读写
	public static function read($filename){
		$log_file_name = P_DIR.self::$dir.$filename;
		if(!file_exists($log_file_name)){
			Y_Debug::addMsg($log_file_name.'日志文件加载失败！');
			return false;
		}
		$log_content = file_get_contents($log_file_name);
		$list_str_array = explode("\r\n",$log_content);
		unset($log_content);
		//去除最后一行空行
		array_pop($list_str_array);
		if(empty($list_str_array)) return false;
		foreach($list_str_array as $key=>$val){
			$val = explode("\t",$val);
			array_shift($val);
			//去除第一
			$data[$key] = $val;
		}
		return $data;
	}
	//根据日志类型获取类型日志列表
	public static function getList($type){
		$fp = opendir(P_DIR.self::$dir);
		readdir($fp);
		readdir($fp);
		$data = array();
		while(false!=($file=readdir($fp))){
			$pattern = '/^(\d{5,})_(\S+)\.php$/';
			preg_match($pattern,$file,$arr);
			if($arr[2]==$type){
				$data[] = $arr[1];
			}	
		}
		return $data;
	}
	//注册错误系统
	public static function handler($errno,$errstr,$errfile,$errline){
		if(!(error_reporting() & $errno)){
			return;
		}
		switch($errno){
			case E_USER_ERROR:
				$level=2;
				break;
			case E_USER_WARNING:
				$level=1;
				break;
			case E_USER_NOTICE:
				return;
			default:
				$level=3;
				break;
		}
		$msg = '['.$errstr.']在文件'.$errfile.'在第'.$errline.'中';
		self::write($msg,'error',$level);
	}
}
?>
