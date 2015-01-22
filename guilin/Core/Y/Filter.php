<?php
/**
 * 数据过滤
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-20
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
class Y_Filter{
	//输出安全的html
	public static function html($text, $tags = null) {
		$text = trim($text);
		//完全过滤注释
		$text = preg_replace('/<!--?.*-->/','',$text);
		//完全过滤动态代码
		$text =	preg_replace('/<\?|\?'.'>/','',$text);
		//完全过滤js
		$text =	preg_replace('/<script?.*\/script>/','',$text);
		$text =	str_replace('[','&#091;',$text);
		$text =	str_replace(']','&#093;',$text);
		$text =	str_replace('|','&#124;',$text);
		//过滤换行符
		$text =	preg_replace('/\r?\n/','',$text);
		//br
		$text = preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
		$text =	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
		//过滤危险的属性，如：过滤on事件lang js
		while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
			$text=str_replace($mat[0],$mat[1],$text);
		}
		while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
			$text=str_replace($mat[0],$mat[1].$mat[3],$text);
		}
		if(empty($tags)) {
			$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
		}
		//允许的HTML标签
		$text =	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
		//过滤多余html
		$text =	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
		//过滤合法的html标签
		while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
			$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
		}
		//转换引号
		while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
			$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
		}
		//过滤错误的单个引号
		while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
			$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
		}
		//转换其它所有不合法的 < >
		$text =	str_replace('<','&lt;',$text);
		$text =	str_replace('>','&gt;',$text);
		$text =	str_replace('"','&quot;',$text);
		//反转换
		$text =	str_replace('[','<',$text);
		$text =	str_replace(']','>',$text);
		$text =	str_replace('|','"',$text);
		//过滤多余空格
		$text =	str_replace('  ',' ',$text);
		return $text;
	}
	//防止xss攻击
	public static function xss($val) {
		$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++) {
			$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
			$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }
		$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);
		$found = true; // keep replacing as long as the previous round replaced something
		while ($found == true) {
			$val_before = $val;
			for ($i = 0; $i < sizeof($ra); $i++) {
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
				$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
				if ($val_before == $val) {
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
		return $val;
	}
	
}
?>
