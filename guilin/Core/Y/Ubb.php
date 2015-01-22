<?php
/**
 * UBB过滤
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-17
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Y_Ubb{
	static public $dir = 'public/js/editor/face/';
	static public function init($str,$flag=false){
		$str = trim($str);
		if(!$str) return $str;

		
		if($flag){
			$str = htmlspecialchars($str);
		}
		//制表符号
		$str = preg_replace("/\\t/is","  ",$str);
		//加粗
		$str = preg_replace("/\[b\](.+?)\[\/b\]/","<b style='font-weight:600'>\\1</b>",$str);
		//换行
		$str = preg_replace("/\[br\]/","<br/>",$str);
		//斜体
		$str=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$str);
		//下划线
		$str=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$str);
		//引用
		$str=preg_replace("/\[q\](.+?)\[\/q\]/is","<q>\\1</q>",$str);
		//颜色
		//$str=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/","<span style='color:\\1'>\\2</span>",$str);
		//大小
		//$str=preg_replace("/\[size=(\d+)\](.+?)\[\/szie\]/","<span style='font-size:\\1px'>\\2</span>",$str);
		//表情
		$str=preg_replace("/\[emot\](.+?)\[\/emot\]/","<img src='".self::$dir."\\1.gif' title='\\1' />",$str);
		//标题
		//$str=preg_replace("/\[h\](.+?)\[\/h\]/","<h3>\\1</h3>",$str);
		$str=preg_replace("/\[url href=(.+?)\]([^\[]*)\[\/url\]/is","<a href='\\1' target=_blank>\\2</a>",$str);
  		//$str=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$str);
		//换行
		$str=preg_replace("/\\n/is",'<br/>',$str);
		return $str;
	}
	//ubb相反
	public static function toUbb($str){
		$str = trim($str);
		if(!$str) return $str;
		$str = str_replace("\n", '', $str);
		$str = preg_replace("/\<I>(.+?)\<\/I>/i", "[i]\\1[/i]",$str);
		$str = preg_replace("/\<b[^>]+>(.+?)<\/b>/i", "[b]\\1[/b]", $str);
		$str = preg_replace("/\<u>(.+?)\<\/u>/i", "[u]\\1[/u]", $str);
		$str = preg_replace("/\<q>(.+?)\<\/q>/i", "[q]\\1[/q]", $str);
		$str = preg_replace("/\<a.*?[^>]+href=['\"]([^'\"]+)['\"][^'\"]*>(.+?)\<\/a>/is", "[url href=\\1]\\2[/url]", $str);
		$str = preg_replace("/\<br[\/]?\>/i","[br]",$str);
		$str = preg_replace("/\<img.*?[^>]+title=['\"]([a-z]+)['\"][^'\"]*\/>/is", "[emot]\\1[/emot]", $str);
		return $str;
	}
}
?>
