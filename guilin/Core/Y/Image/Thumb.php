<?php
/**
 * 图像缩略
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-23
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Y_Image_Thumb extends Y_Image{
	//图象缩放
	public static function thumb($image,$thumbname,$maxWidth=100,$maxHeight=100,$x=0,$y=0,$interlace=true,$type=''){
		$info = parent::getImageInfo($image);
		if($info!==false){
			$srcWidth = $info['width'];
			$srcHeight = $info['height'];
			$type = $type?strtolower($type):$info['type'];
			$type = $type=='jpg'?'jpeg':$type;
			$interlace = $interlace?1:0;
			unset($info);
			if($x==0 && $y==0){
				$scale = min($maxWidth/$srcWidth,$maxHeight/$srcHeight);
				if($scale>=1){
					//超过则不缩放
					$width = $srcWidth;
					$height = $srcHeight;
				}else{
					$width = (int)($srcWidth*$scale);
					$height = (int)($srcHeight*$scale);
				}
			}else{
				$width = $srcWidth;
				$height = $srcHeight;
				$srcWidth = $maxWidth;
				$srcHeight = $maxHeight;
			}
			$createFun = 'imagecreatefrom'.$type;
			$srcImg= $createFun($image);
			//这里我动一下
			if($type!='gif' && function_exists('imagecreatetruecolor'))
				$thumbImg = imagecreatetruecolor($width,$height);
			else
				$thumbImg = imagecreate($width,$height);
			 
			//$thumbImg = imagecreate($maxWidth,$maxHeight);
			//背景色
			//imagecolorallocate($thumbImg,255,255,255);
			//$x1 = ($maxWidth-$width)/2;
			//$y1 = ($maxHeight-$height)/2;
			//复制图片
			if(function_exists('imagecopyresampled'))
				imagecopyresampled($thumbImg,$srcImg,0,0,$x,$y,$width,$height,$srcWidth,$srcHeight);
			else
				imagecopyresized($thumbImg,$srcImg,0,0,$x,$y,$width,$height,$srcWidth,$srcHeight);
			if('gif'==$type || 'png'==$type){
				$background_color = imagecolorallocate($thumbImg,0,255,0);
				imagecolortransparent($thumbImg,$background_color);
			}
			if('jpg'==$type || 'jpeg'==$type)
				imageinterlace($thumbImg,$interlace);
			$imageFun = 'image'.$type;
			$imageFun($thumbImg,$thumbname);
			imagedestroy($thumbImg);
			imagedestroy($srcImg);
			return $thumbname;
		}else
			return false;
	}
}
?>
