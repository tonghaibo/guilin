<?php
/**
 * 格式化输出
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-20
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
class Y_Pr{
	//获取播放时间描叙
	static public function times($time){
		$m = 60;
		$f = $m*60;
		if($time<$m) return $time.'秒';
		if($time>=$m and $time<$f){
			$minuts = round($time/$m,0).'分';
			$miao = $time%$m.'秒';
			return $minuts.$miao;
		}
		if($time>=$f){
			$house = round($time/$f,0).'时';
			$minuts = round($time%$f/$m,0).'分';
			$miao = $time%$m.'秒';
			return $house.$minuts.$miao;
		}
	}
	//大小格式化输出
	public static function size($size,$dec=2){
		$a = array('B','KB','MB','GB','TB','PB');
		$pos = 0;
		while($size>=1024){
			$size /= 1024;
			$pos++;
		}
		return round($size,$dec).' '.$a[$pos];
	}
	//字符串截取输出
	public static function substr($str,$start=0,$len,$suffix=true,$charset='utf-8'){
		if(function_exists('mb_substr')){
			$slice = mb_substr($str,$start,$len,$charset);
		}else if(function_exists('iconv_substr')){
			$slice = iconv_substr($str,$start,$len,$charset);
			if(false === $slice){
				$slice = '';
			}
		}else{
			$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($re[$charset], $str, $match);
			$slice = join("",array_slice($match[0], $start, $len));
		}
		return $suffix?$slice.'...':$slice;
	}
	//私密输出 字符 保留位数 补位字符
	public static function strCover($str,$len,$t='*',$char='utf-8'){
		if(function_exists('mb_strlen')){
			$strlen = mb_strlen($str,$char);
		}else if(function_exists('iconv_strlen')){
			$strlen = iconv_strlen($str,$char);
		}else{
			$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($re[$charset], $str, $match);
			$strlen = count($match[0]);
		}
		if($strlen<=$len) return $str;
		$str = self::substr($str,0,$len,false,$char);
		$t = str_pad($t,$strlen-$len,$t,STR_PAD_RIGHT);
		return $str.$t;
	}
	//Html转义
	public static function enHtml($str){
		return htmlspecialchars($str);		
	}
	//过滤特性字符
	public static function badStr($str,$flag=true){
		if(empty($str)) return false;
		if($flag){
			$str = self::html($str);
		}
		$arr = array("'","\x00","%00","\"","=",'#',"\$",'>',"\\",'*','%','(',')',';');
		return str_replace($arr,'',trim($str));
	}
	//图像输出
	public static function image($url,$w=100,$h=100){
		if(!file_exists($url)){
			//本身不存在
			$url = P_DIR.'public/images/noimg.jpg';
		}
		//$path = explode('/',$url);
		//$filename = array_pop($path);
		//$dir = implode('/',$path).'/';
		//$newname = $dir.$w.'_'.$h.'@'.$filename;
		$newname = $url.'@'.$w.'_'.$h.'.jpg';
		if(file_exists($newname)){
			return $newname;
		}else{
			Y_Image_Thumb::thumb($url,$newname,$w,$h);
			return $newname;
		}
	}
	//过滤输出所有HTML标签只提取文本
	public static function html($text){
		$text = trim($text);
		//空白
		$text = str_replace(' ','',$text);
		//换行
		$text = str_replace("\n",'',$text);
		$text = str_replace("\t",'',$text);
		$text = str_replace("\r",'',$text);
		//注释
		$text = preg_replace('/<!--?.*-->/','',$text);
		//动态代码
		$text = preg_replace('/<\?|\?'.'>/','',$text);
		//完全过滤js
		$text =	preg_replace('/<script?.*\/script>/','',$text);
		$text = preg_replace('/<.*?>/','',$text);
		$text = preg_replace('/&.*?;/','',$text);
		//未完
		return $text;
	}
}
?>
