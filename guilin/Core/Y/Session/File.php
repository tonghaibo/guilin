<?php
/**
 * Y框架SESSION保存文件
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-10-25
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Y_Session_File{
	public static function start(){
		$session_dir = P_DIR.'runtime/session/';
		if(is_dir($session_dir) && is_writeable($session_dir)){
			session_save_path($session_dir);
		}
		register_shutdown_function(array(__CLASS__,'close'));
		session_start();
	}
	private static function close(){
		if(session_id()){
			session_write_close();
		}
	}
}
?>
