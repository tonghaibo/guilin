<?php
/**
 * Y关键基础类
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-20
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
class Y_Widget{
	//参数
	//显示
	public static function display($filename,$_data=array()){
		$filename = A_DIR.'widget/'.$filename.'.html';
		if(file_exists($filename)){
			ob_start();
			extract($_data,EXTR_OVERWRITE);
			unset($_data);
			include $filename;				
			echo ob_get_clean();
		}else{
			Y_Debug::addMsg('[ 挂件 ]: '.$filename.'加载失败！');
		}
	}
}
?>
