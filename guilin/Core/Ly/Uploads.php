<?php
/**
 * 附件操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Uploads{
	//上传插入
	public static function insert($name,$issys=0,$des=null){
		$info = Y_Upload::upload($name);
		if($info['error']){
			//失败
			return false;
		}else{
			if($issys){
				$data['issys'] = 1;
				$data['uid'] = Y_Session::get('G_id');
			}else{
				$data['issys'] = 0;
				$data['uid'] = Y_Session::get('uid');
			}
			if($des){
				$data['des'] = $des;
			}
			$data['path'] = $info['info'];
			$data['suffix'] = $info['sub'];
			$data['md5file'] = md5_file($info['info']);
			//$data['name'] = $info['oldname'];
			$data['size'] = $info['size'];
			$data['uptime'] = time();
			$model = Y_Db::init('uploads');
			if($res['id']=$model->insert($data)){
				$res['path'] = $info['info'];
				return $res;
			}
			return false;

		}
	}
	//获取图片地址和描叙
	public static function getPath($id){
		$model = Y_Db::init('uploads');
		$model->field(array('path','des'));
		return $model->where(array('status<'=>1,'id'=>$id))->find();
	}
	//修改
	public static function update($id,$data){
		if($id && $data){
			$model = Y_Db::init('uploads');
			$model->where(array('id'=>$id));
			return $model->update($data);
		}
		return false;
	}
	//获取url
	public static function getUrl($id){
		$model = Y_Db::init('uploads');
		$model->field('path');
		$url = $model->where(array('status<'=>1,'id'=>$id))->find();
		return $url['path'];
	}
	//获取ID
	public static function getId($path){
		$model = Y_Db::init('uploads');
		$model->field('id');
		$url = $model->where(array('status<'=>1,'path'=>$path))->find();
		return $url['id'];
	}
	//获取上传者名称
	public static function getName($uid,$sys){
		$name = false;
		if($sys){
			$name = Ly_Admin::getName($uid);
		}else{
			$name = Ly_User::getEmail($uid);
		}
		return $name;
	}
	//删除
	public static function delete($id){
		if($id){
			$model = Y_Db::init('uploads');
			$name = $model->find($id);	
			if($name=$name['path']){
				$model->delete($id);
				//删除文件
				$dir = explode('/',$name);
				$file = array_pop($dir);
				$dir = join('/',$dir);
				if(file_exists($dir)){
					$fp = opendir($dir);
					readdir($fp);
					readdir($fp);
					while(($f=readdir($fp))!==false){
						$arr = explode('.',$f);
						$file = explode('.',$file);
						$file = array_shift($file);
						if(in_array($file,$arr)){
							@unlink($dir.'/'.$f);
						}
					}
					closedir($fp);
					return true;
				}	
			}
		}
		return false;
	}
}
?>
