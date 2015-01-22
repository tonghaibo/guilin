<?php
class File{
	static $path;

	function __construct(){
		
		session_set_save_handler(array(__CLASS__,'open'),
			array(__CLASS__,'close'),
			array(__CLASS__,'read'),
			array(__CLASS__,'write'),
			array(__CLASS__,'destroy'),
			array(__CLASS__,'gc')
		
		);
	
	}

	static  function open($path){
		self::$path=$path;
	}


	static  function close(){
		return true;
	}


	static  function read($sid){
		$path=self::$path.'/my_'.$sid;
		
		if(file_exists($path)){

			return file_get_contents($path);
		}else{

			return false;
		}

	}


	static  function write($sid,$data){

		$path=self::$path.'/my_'.$sid;

		$string=serialize($data);

		file_put_contents($path,$string);


	}

	static  function destroy($sid){
		$path=self::$path.'/my_'.$sid;

		if(file_exists($path)){

			unlink($path);
		}

	}


	static  function gc($maxtime){
	

		$arr=glob('my_*');

		foreach($arr as $value){
			if(filectime($value)+$maxtime<time()){

				unlink($value);
			}

		}

	}
}

?>
