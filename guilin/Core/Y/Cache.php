<?php
/**
 * Y裤架缓存类，生产静态文件
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-20
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Y_Cache{
	//默认两分钟
	public static $cachetime = 2;
	//目录：html/控制器 方法名_参数
	//@param name 模版名称或者
	public static function set($name=null){
		if(Y_CACHE && Y_CACHE==true){
		$dir = A_DIR.'html/'.Y::$controller.'/';
		if(!is_dir($dir)){
			mkdir($dir,'0755',true);
		}
		$filename = $name?$name:md5($_SERVER['REQUEST_URI']);
		$filename .= '.html';
		//判断是否需要更新缓存
		if(!self::getTime($dir.$filename)){
			//缓存输出文件
			$content = ob_get_flush();
			$content = self::trim($content);
			file_put_contents($dir.$filename,$content);	
		}
		}
	}
	//获取文件
	public static function get($name=null){
		if(Y_CACHE && Y_CACHE==true){
			$dir = A_DIR.'html/'.Y::$controller.'/';
			$filename = $name?$name:md5($_SERVER['REQUEST_URI']);
			$filename .= '.html';
			if(self::getTime($dir.$filename)){
				$content = file_get_contents($dir.$filename);
				echo $content;
				die;
			}
		}		
	}
	//获取文件的缓存时间
	public static function getTime($filename){
		if(file_exists($filename)){
			$mtime = filemtime($filename);
			$time = defined('Y_CACHETIME')?Y_CACHETIME:self::$cachetime;
			$time = $time*60;
			if((time()-$mtime)>$time){
				return false;
			}else{
				//未过期
				return true;
			}

		}else{
			return false;
		}
	}
	//去除代码中的空白和注释
	public static function trim($content){
		$stripStr = '';
		//分析php源代码
		$tokens = token_get_all($content);
		$last_space = false;
		for($i=0,$j=count($tokens);$i<$j;$i++){
			if(is_string($tokens[$i])){
				$last_space = false;
				$stripStr .= $tokens[$i];
			}else{
				switch($tokens[$i][0]){
					//注释去除
					case T_COMMENT:
					case T_DOC_COMMENT:
						break;
					//空格过滤
					case T_WHITESPACE:
						if(!$last_space){
							$stripStr .= ' ';
							$last_space = true;
						}
						break;
					case T_START_HEREDOC:
						$stripStr .= "<<<Y\n";
						break;
					case T_END_HEREDOC:
						$stripStr .= "Y;\n";
						for($k=$i+1;$k<$j;$k++){
							if(is_string($tokens[$k]) && $tokens[$k]==';'){
								$i=$k;
								break;
							}else if($tokens[$k][0]==T_CLOSE_TAG){
								break;
							}
						}
						break;
					default:
						$last_space = false;
						$stripStr .=$tokens[$i][1];
				}
			}
		}
		return $stripStr;
	}
}
?>
