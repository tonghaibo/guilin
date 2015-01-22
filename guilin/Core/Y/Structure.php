<?php
/**
 * 自动创建APP包含目录
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) exit();
class Y_Structure{
	//app目录创建
	public static function runtime(){
		$dirs = array(
			A_DIR.'controllers/',
			A_DIR.'views/',
			A_DIR.'html/',
			A_DIR.'widget/',
			P_DIR.'runtime/',
			P_DIR.'runtime/logs/',
			//P_DIR.'runtime/cache/',
			P_DIR.'runtime/data/',
			P_DIR.'public/',
			P_DIR.'public/images/',
			P_DIR.'public/css/',
			P_DIR.'public/js/',
			P_DIR.'public/uploads/',
			P_DIR.'config/',
			P_DIR.'language/'
		);
		foreach($dirs as $dir){
			if(!file_exists($dir)){
				mkdir($dir,'0755');
			}
		}
	}
}
?>
