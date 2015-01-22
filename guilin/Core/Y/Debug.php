<?php
/**
 * debug调试类
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) exit();
class Y_Debug{
	public static $includefile = array();
	public static $info = array();
	public static $sqls = array();
	public static $startTime;
	public static $stopTime;
	public static $msg = array(
		E_WARNING=>'[Waring]',
		E_NOTICE=>'[Notice]',
		E_STRICT=>'[Strict]',
		E_USER_ERROR=>'[User_Error]',
		E_USER_WARNING=>'[User_Warning]',
		E_USER_NOTICE=>'[User_Notice]',
		'Unkown'=>'Unkown_Error',
	);
	public static function start(){
		self::$startTime = microtime(true);
	}
	public static function stop(){
		self::$stopTime = microtime(true);
	}
	public static function spent(){
		return round((self::$stopTime - self::$startTime),4);
	}
	//错误hander
	public static function handler($errno,$errstr,$errfile,$errline){
		if(!isset(self::$msg[$errno]))
			$errno = 'Unkown';
		if($errno==E_NOTICE || $errno==E_USER_NOTICE)
			$color = '#881';
		else
			$color = 'red';
		$mess = '<font color='.$color.'>';
		$mess .= '<b>'.self::$msg[$errno]."</b>[在文件{$errfile}]中,第 $errline 行:";
		$mess .= $errstr;
		$mess .='</font>';
		self::addMsg($mess);
	}
	// 添加消息
	public static function addMsg($msg,$type=0,$level=NULL){
		if(defined('Y_DEBUG') && Y_DEBUG==true){
			switch($type){
				case 0:
					self::$info[] = '<span style="color:red;font-size:14px;">'.$msg.'</span>';
					break;
				case 1:
					self::$includefile[] = '<span style="color:green;font-size:14px;">'.$msg.'</span>';
					break;
				case 2:
					self::$sqls[] = '<span style="color:green;font-size:14px;">'.$msg.'</span>';
					break;
			}
		}else{
			//写进错误日志
			if($type==0){
				//系统报错
				Y_Log::write($msg,'error');
			}
		}
	}
	//展示
	public static function show(){
		echo '<div style="clear:both;text-align:left;font-size:11px;color:#666;margin:10px;padding:10px;background:#F5F5F5;border:1px dotted #778855;z-index:1000;font-weight:normal">';
		echo '<div><span style="float:left;width:200px;"><b>运行信息</b>( <font color="red">'.self::spent().' </font>秒):</span><span onclick="this.parentNode.parentNode.style.display=\'none\'" style="cursor:pointer;float:right;width:35px;background:green;border:1px solid #555;color:white">关闭X</span></div><br>';
		echo '<ul style="margin:0px;padding:0 10px 0 10px;list-style:none">';
		if(count(self::$includefile) > 0){
			echo '［自动包含］';
			foreach(self::$includefile as $file){
				echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.$file.'</li>';
			}		
		}
		if(count(self::$info) > 0 ){
			echo '<br>［系统信息］';
			foreach(self::$info as $info){
				echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.$info.'</li>';
			}
		}
		if(count(self::$sqls) > 0) {
			echo '<br>［SQL语句］';
			foreach(self::$sqls as $sql){
				echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.$sql.'</li>';
			}
		}
		echo '<br>[内存占用]';
		echo '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.Y_Pr::size(memory_get_usage()).'</li>';
		echo '</ul>';
		echo '</div>';	
	}
	
}
?>
