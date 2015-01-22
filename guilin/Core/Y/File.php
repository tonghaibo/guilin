<?php
/**
 * Y框架文件即目录操作
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-24
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Y_File{
	const LIST_FILE = 'file';
	const LIST_FOLDER = 'folder';
    	public $chmod;

    	public function  __construct($chmod=0777) {
        	$this->chmod = $chmod;
    	}
    /**
     * Get the space used up by a folder recursively.
     * @param string $dir Directory path.
     * @param string $unit Case insensitive units: B, KB, MB, GB or TB
     * @param int $precision 
     * @return float total space used up by the folder (KB)
     */
    public static function getSize($path){
	    $path = str_replace('\\', '/', $path);
	    $path = rtrim($path,'/').'/';
	    $totalSize = 0;
	    $handle = opendir($path);
	    readdir($handle);
	    readdir($handle);
	    while(false !== ($file = readdir($handle))){
                if (is_dir($path.$file)){
			$totalSize += self::getSize($path.$file);
		}else{
                    	$totalSize += filesize($path.$file);
		}
	}
	closedir($handle);
        return $totalSize;
    }
    /**
     * Get a list of files with its path in a directory (recursively)
     * @param string $path
     * @return array 
     */
    public static function getList($path,$flag=false){
        $path = str_replace('\\', '/', $path);
	$path = rtrim($path,'/').'/';
	if(!is_dir($path)){
		return false;
	}
        $handle = opendir($path);
	$rs = array();
        while (false !== ($file = readdir($handle))){
            if ($file != '.' && $file != '..' && $file!='.svn'){
		    $info['name'] = iconv('gbk','utf-8',$file);
		    if(is_dir($path.$file) && $flag){
		    		$info['size'] = self::getSize($path.$file);
		    }else{
			    	$info['size'] = filesize($path.$file);
		    }
		    $info['type'] = filetype($path.$file);
		    //访问时间
		    $info['atime'] = fileatime($path.$file);
		    //修改时间可以理解创建
		    $info['ctime'] = filectime($path.$file);
		    //文件内容修改时间
		    $info['mtime'] = filemtime($path.$file);
		    $info['ext'] = self::getExt($file);
		    $rs[] =$info;
	    }
	}
        closedir($handle);
        return $rs;
    }

    //删除
    public static function delete($dir){
	    $fp = opendir($dir);
	    readdir($fp);
	    readdir($fp);
	    while(($file=readdir($fp))!==false){
		    $path = $dir.'/'.$file;
		    if(is_dir($path)){
			    self::delete($path);
			    rmdir($path);
		    }else{
		    	unlink($path);
		    }
	    }
	    closedir($fp);
    }
    //获取后缀
    public static function getExt($name){
	    if(preg_match('/(.*)\.(.+)/',$name,$arr)){
	    	return strtolower($arr[2]);
	    }
	    return false;
    }
    //可编辑
    public static function isFile($ext){
	    $arr = array('txt','js','css','php','html','ini','htaccess');
	    return in_array($ext,$arr);
    }
    //图片
    public static function isImage($ext){
	    $arr = array('jpg','gif','png','jpeg','bmp');
	    return in_array($ext,$arr);
    }
    //是否是正常文件
    public static function isOk($ext){
	    if(self::isFile($ext) || self::isImage($ext)){
	    	return $ext;
	    }
	    return false;
    }
}
?>
