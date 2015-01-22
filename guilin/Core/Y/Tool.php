<?php
/**
 * 基本工具，打印调试等
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class Y_Tool{
	//打印
	public static function P($var,$exit=false){
		ob_start();
		$style='<style>pre#debug{margin:10px;font-size:13px;color:#222;font-family:Consolas;line-height:1.2em;background:#f6f6f6;border-left:5px solid #444;padding:5px;word-break:break-all;}pre#debug b{font-weight:400;}#debug #debug_str{color:#E75B22;}#debug #debug_keywords{font-weight:800;color:00f;}#debug #debug_tag1{color:#22f;}#debug #debug_tag2{color:#f33;font-weight:800;}#debug #debug_var{color:#33f;}#debug #debug_var_str{color:#f00;}#debug #debug_set{color:#0C9CAE;}</style>';
		if (is_array($var)){
			print_r($var);
		}else if(is_object($var)){
			echo get_class($var)." Object";
		}else if(is_resource($var)){
			echo (string)$var;
		}else{
			echo var_dump($var);
		}   
		$out = ob_get_clean();//缓冲输出给$out 变量
		$out=preg_replace('/"(.*)"/','<b id="debug_var_str">"'.'\\1'.'"</b>',$out);//高亮字符串变量
		$out=preg_replace('/=\>(.*)/','=>'.'<b id="debug_str">'.'\\1'.'</b>',$out);//高亮=>后面的值
		$out=preg_replace('/\[(.*)\]/','<b id="debug_tag1">[</b><b id="debug_var">'.'\\1'.'</b><b id="debug_tag1">]</b>',$out);//高亮变量
		$from = array('    ','(',')','=>');
		$to   = array('  ','<b id="debug_tag2">(</i>','<b id="debug_tag2">)</b>','<b id="debug_set">=></b>');
		$out=str_replace($from,$to,$out);  
		$keywords=array('Array','int','string','class','object','null');//关键字高亮
		$keywords_to=$keywords;
		foreach($keywords as $key=>$val)
		{  
			$keywords_to[$key] = '<b id="debug_keywords">'.$val.'</b>';
		}
		$out=str_replace($keywords,$keywords_to,$out); 
		echo $style.'<pre id="debug"><b id="debug_keywords">'.self::getVname($var).'</b> = '.$out.'</pre>';
		if ($exit)  exit;//为真则退出	
	}
	private static function getVname(&$aVar){
		foreach($GLOBALS as $key=>$var){
			if($aVar==$GLOBALS[$key] && $key!="argc"){
				return $key;
			}
		}
	}	
}
?>
