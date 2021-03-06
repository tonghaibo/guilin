<?php
class ms{
	static $mem;
	static $maxtime;

	function __construct($mem){
		self::$mem=$mem;
		self::$maxtime=ini_get('session.gc_maxlifetime');
		
		session_set_save_handler(
			array(__CLASS__,'open'),
			array(__CLASS__,'close'),
			array(__CLASS__,'read'),
			array(__CLASS__,'write'),
			array(__CLASS__,'destroy'),
			array(__CLASS__,'gc')
			
		);
		session_start();
		

	}

	static function  open($sid){
		return true;
	}

	static function close(){
		return true;
	}

	static function read($sid){
		return self::$mem->get($sid);
	}

	function write($sid,$data){
		return self::$mem->set($sid,$data,MEMCACHE_COMPRESSED,self::$maxtime);	
	}

	function destroy($sid){

		return self::$mem->delete($sid);
	}


	function gc($maxtime){
		
		return true;
	}

}

$m=new Memcache();

$m->addServer('127.0.0.1');

$session=new ms($m);

//$_SESSION['hello']='你好万维网';

echo $_SESSION['hello'];



?>
