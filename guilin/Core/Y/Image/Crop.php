<?php
/**
 * 图像裁剪
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-23
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Y_Image_Crop extends Y_Image{
	//裁剪图片 源地址图片，目的图片，类型，宽，高，截取开始的x,y点
	public static function crop($image,$thumbName,$x1=0,$y1=0,$x2=100,$y2=100,$w=100,$h=100,$type=''){
		$info = parent::getImageInfo($image);
		$width = $info['width'];
		$height = $info['height'];
		$type = $type?$type:$info['type'];
		$type = strtolower($type);
		$type = ($type=='jpg')?'jpeg':$type;
		$imageFun = "imagecreatefrom{$type}";
		$oriImg = $imageFun($image);
		//按指定大小 创建图片
		$thumbImg = imagecreatetruecolor($w,$h);
		imagecopyresampled($thumbImg,$oriImg,0,0,$x1,$y1,$w,$h,$x2-$x1,$y2-$y1);
		if($type == 'gif' || $type == 'png'){
       			$backColor = imagecolorallocate($thumbImg, 0, 255, 0);
       			imagecolortransparent($thumbImg, $backColor); // 设置为透明色
       		}else if($type == 'jpeg'){
       			imageinterlace($thumbImg, 1);
		}
		$imageFun = "image{$type}";
		$return = $imageFun($thumbImg,$thumbName);
		imagedestroy($oriImg);
		imagedestroy($thumbImg);
		return $return;
	}	
}
?>
