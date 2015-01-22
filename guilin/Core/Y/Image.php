<?php
/**
 * Y 框架图片处理类
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-23
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class Y_Image{
	//获取图片信息
	public static function getImageInfo($img){
		$imageinfo = getimagesize($img);
		if($imageinfo!==false){
			$imageType = strtolower(substr(image_type_to_extension($imageinfo[2]),1));
			$imageSize = filesize($img);
			$info = array(
				'width'=>$imageinfo[0],
				'height'=>$imageinfo[1],
				'type'=>$imageType,
				'size'=>$imageSize,
				'mime'=>$imageinfo['mime']
			);
			return $info;
		}else
			return false;
	}
	//显示图片
	public static function output($im,$type='png',$filename=''){
		header('Content-type:image/'.$type);
		$ImageFun='image'.$type;
		if(empty($filename)){
			$ImageFun($im);
		}else{
			$ImageFun($im,$filename);
		}
		imagedestroy($im);
	}
	
}
?>
