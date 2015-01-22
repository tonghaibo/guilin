<?php
/**
 * 常用检测
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-20
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
class Y_Check{
	//邮箱检测
	public static function email($str){
		return 6 < strlen( $str ) && preg_match( "/^[\\w\\-\\.]+@[\\w\\-\\.]+(\\.\\w+)+\$/", $str );
	}
	//url检测
	public static function url($str) {	
		if (!$str) {
			return false;
		}		
		return preg_match('#(http|https|ftp|ftps)://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?#i', $str) ? true : false;
	}
	//邮政编码
	public static function postNum($num) {	
		if (!$num) {
			return false;
		}	
		return preg_match('#^[1-9][0-9]{5}$#', $num) ? true : false;
	}
	//身份证
	public static function pCard($num) {		
		if (!$num) {
			return false;
		}		
		return preg_match('#^[\d]{15}$|^[\d]{18}$#', $num) ? true : false;
	}
	//IP检测
	public static function ip($str) {		
		if (!$str) {
			return false;
		}		
		if (!preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $str)) {
			return false;			
		}		
		$ip_array = explode('.', $str);		
		//真实的ip地址每个数字不能大于255（0-255）		
		return ($ip_array[0]<=255 && $ip_array[1]<=255 && $ip_array[2]<=255 && $ip_array[3]<=255) ? true : false;
	}
	//手机号码
	public static function mobile($num) {
		
		if (!$num) {
			return false;
		}	
		return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $num) ? true : false;
	}
	//是否是utf-8字符
	public static function utf8($string) {
		return preg_match('%^(?:
			[\x09\x0A\x0D\x20-\x7E]            # ASCII
			| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
			|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
			| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
			|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
			|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
			| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
			|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
		)*$%xs', $string);
	}
	//是否是字符串
	public static function str($str){
		$str = trim($str);
		if(empty($str)) return false;
		return $str;
	}
	//是否是数字
	public static function num($num){
		if(is_numeric($num)){
			return true;
		}else{
			return false;
		}
	}
	//QQ号码检测
	public static function qq($str){
		return preg_match('/^[1-9][0-9]{4,}$/',$str);
	}
	//密码验证
	public static function password($str){
		return preg_match('/^\S{6,20}$/',$str);
	}
	//长度
	public static function len($str,$min=1,$max=null,$type='utf-8'){
		$str = trim($str);
		$len = 0;
		if(function_exists('mb_strlen')){
			$len = mb_strlen($str,$type);
		}else{
			$len = strlen($str);
		}
		if($len>=$min){
			if($max && $max>$min){
				if($len<=$max) return true;
			}else{ 
		       		return true;
			}
		}
		return false;
	}
}
?>
