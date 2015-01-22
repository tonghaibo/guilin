<?php
/**
 * 文件上传
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-24
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Y_Upload{
	protected static $path = 'public/uploads/';
	protected static $allowtype = array('jpg','gif','png','jpeg');
	protected static $allowmime = array('image/jpe','image/jpeg','image/pjpeg','image/gif','image/png','image/x-png');
	protected static $allowsize = 2048000;
	public static function upload($name='file'){
		$file = $_FILES[$name];
		$data = array();
		if($file['error']){
			switch($file['error']){
				case 1:
				case 2:
					$data['error'] = 1;
					$data['info'] = '大小超出配置大小';
					break;
				default:
					$data['error'] = 2;
					$data['info'] = '图片上传失败';
					break;
			}
			return $data;
		}
		//大小检测
		if($file['size']>self::$allowsize){
			$data['error'] = 3;
			$data['info'] = '图片大小超出指定大小';
			return $data;
		}
		//后缀名合法
		$sub = strtolower(array_pop(explode('.',$file['name'])));
		if(!in_array($sub,self::$allowtype)){
			$data['error'] = 4;
			$data['info'] = '图片后缀类型不合法';
			return $data;
		}
		//mime类型判断
		if(!in_array($file['type'],self::$allowmime)){
			$data['error'] = 5;
			$data['info'] = '图片MIME类型不合法';
			return $data;
		}
		if(!is_uploaded_file($file['tmp_name'])){
			$data['error'] = 6;
			$data['info'] = '图片非法上传';
			return $data;
		}
		$dir = date('Ym').'/'.date('d').'/';
		$dir = self::$path.$dir;
		if(!file_exists($dir)){
			@mkdir($dir,0777,true);
			@touch($dir.'/index.html');
		}
		$newname = uniqid().'.'.$sub;
		if(move_uploaded_file($file['tmp_name'],$dir.$newname)){
			$data['error'] = 0;
			$data['info'] = $dir.$newname;
			//增加信息
			$data['sub'] = $sub;
			$data['size'] = $file['size'];
			$data['oldname'] = $file['name'];
			return $data;
		}else{
			$data['error'] = 7;
			$data['info'] = '移动文件失败';
			return $data;
		}
	}
	public static function upfile($name,$dir='public/',$flag=false){
		$allow = array('jpeg','jpg','gif','png','txt','js','css');
		$mime = array('image/jpe','image/jpeg','image/pjpeg','image/gif','image/png','image/x-png','text/css','text/plain','application/x-javascript');
		$file = $_FILES[$name];
		if($file['error']==0){
			//大小
			//后缀
			//mime
			//是否是上传文件
			//是否新路径
			//移动文件
			if(is_uploaded_file($file['tmp_name'])){
				$dir = trim($dir,'/').'/';
				if(!file_exists($dir)){
					@mkdir($dir,0777,true);
					@touch($dir.'/index.html');
				}
				$file['name'] = $flag?uniqid().'.jpg':$file['name'];
				if(move_uploaded_file($file['tmp_name'],$dir.$file['name'])){
					return $dir.$file['name'];
				}
			}
		}
		return false;

	}
}
?>
