<?php
/**
 * 图像水印处理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-24
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Y_Image_Water extends Y_Image{
	//原图像 水印图片 保存的文件名 水印透明度
	static public function water($source,$water,$savename=null,$alpha=80){
		if(!file_exists($source) || !file_exists($water))
			return false;
		$sInfo = parent::getImageInfo($source);
		$wInfo = parent::getImageInfo($water);
		//大小不合适
		if($sInfo['width']<$wInfo['width'] || $sInfo['height']<$wInfo['height'])
			return false;
		//创建图像
		$sCreateFun = 'imagecreatefrom'.$sInfo['type'];
		$sImage = $sCreateFun($source);
		$wCreateFun = 'imagecreatefrom'.$wInfo['type'];
		$wImage = $wCreateFun($water);
		//设定图像混合模式
		imagealphablending($wImage,true);
		//图像位置，默认为右下角对齐
		$posY = $sInfo['height'] - $wInfo['height'];
		$posX = $sInfo['width'] - $wInfo['width'];
		//生产混合图片
		imagecopymerge($sImage,$wImage,$posX,$posY,0,0,$wInfo['width'],$wInfo['height'],$alpha);
		//输出图像
		$ImageFun = 'image'.$sInfo['type'];
		//无名称则用源名称 且删除
		if(!$savename){
			$savename = $source;
			@unlink($source);
		}
		//保存
		$ImageFun($sImage,$savename);
		imagedestroy($sImage);
	}
	//文字水印
	public static function str($source,$string,$savename=null,$alpha=80){
		if(!file_exists($source))
			return false;
		$length = strlen($string);
		$sInfo = parent::getImageInfo($source);
		$sCreateFun = 'imagecreatefrom'.$sInfo['type'];
		$sImage = $sCreateFun($source);
		//图像位置，默认为右下角对齐
		
		//白色
		$color = imagecolorallocate($sImage,255,255,255);
		$color1 = imagecolorallocate($sImage,80,80,80);
		if(function_exists('imagettftext')){
			$w_width = $length*9+12;
			$w_height = 9;
			$posY = $sInfo['height'] - $w_height;
			$posX = $sInfo['width'] - $w_width;
			//先黑底
			imagettftext($sImage,12,0,$posX+1,$posY+1,$color1,Y_DIR.'Image/consola.ttf',$string);
			//白字
			imagettftext($sImage,12,0,$posX,$posY,$color,Y_DIR.'Image/consola.ttf',$string);
			
		}else{
			$w_width = $length*9 +10;
        		$w_height = 22;
			$posY = $sInfo['height'] - $w_height;
			$posX = $sInfo['width'] - $w_width;
			@imagestring($sImage,5,$posX,$posY,$string,$color);
		}
		//生产混合图片
		//imagecopymerge($sImage,$wImage,$posX,$posY,0,0,$wInfo['width'],$wInfo['height'],$alpha);
		//输出图像
		$ImageFun = 'image'.$sInfo['type'];
		//无名称则用源名称 且删除
		if(!$savename){
			$savename = $source;
			@unlink($source);
		}
		//保存
		$ImageFun($sImage,$savename);
		imagedestroy($sImage);
	}
}
?>
