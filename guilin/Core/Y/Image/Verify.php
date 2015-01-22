<?php
/**
 * Y 验证码
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-24
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Y_Image_Verify extends Y_Image{
	static function Verify($length=5,$type='png',$width=120, $height=22,$verifyName='verify') {
		$randval = 'ABCDEFGHJKMNPRSTUVWXYZ2346789';
		$randval = str_shuffle($randval);
		$randval = substr($randval,0,$length);
		Y_Session::set($verifyName,$randval);
        	$width = ($length * 10 + 10) > $width ? $length * 10 + 10 : $width;
        	if($type != 'gif' && function_exists('imagecreatetruecolor')) {
            		$im = imagecreatetruecolor($width, $height);
        	}else{
            		$im = imagecreate($width, $height);
		}
		$arr = array(array(157,200,180),array(220,180,160),array(190,230,170));
		$barr = $arr[mt_rand(0,2)];
        	$backColor = imagecolorallocate($im,$barr[0],$barr[1],$barr[2]);    //背景色（随机）
        	$borderColor = imagecolorallocate($im,200,200,200);                 //边框色
        	imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        	imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		$stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
		//雪花和线
		/*for($i=0;$i<6;$i++){
			$color = imagecolorallocate($im,mt_rand(100,156),mt_rand(0,156),mt_rand(50,156));
			imageline($im,mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height),$color);
		}*/
		for ($i=0;$i<30;$i++) {  
                    	$color = imagecolorallocate($im,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));  
                    	imagestring($im,mt_rand(1,5),mt_rand(0,$width),mt_rand(0,$height),'*',$color);  
		}
		//写字
		$_x = $width/$length;  
             	for($i=0;$i<$length;$i++) {  
                    	$color = imagecolorallocate($im,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156)); 
			imagettftext($im,14,mt_rand(-30,30),$_x*$i+mt_rand(1,3),$height/1.4,$color,Y_DIR.'Image/EXCEL.TTF',$randval[$i]); 
		}
        	parent::output($im,$type);
	}
}
?>
