<?php
	session_set_save_handler('open','close','read','write','destroy','gc');

	
	$path='';

	function open($path){
		$GLOBALS['path']=$path;
	}


	function close(){
		return true;
	}


	function read($sid){
		$path=$GLOBALS['path'].'/my_'.$sid;
		
		if(file_exists($path)){

			return file_get_contents($path);
		}else{

			return false;
		}

	}


	function write($sid,$data){

		$path=$GLOBALS['path'].'/my_'.$sid;

		$string=serialize($data);

		file_put_contents($path,$string);


	}

	function destroy($sid){
		$path=$GLOBALS['path'].'/my_'.$sid;

		if(file_exists($path)){

			unlink($path);
		}

	}


	function gc($maxtime){
	

		$arr=glob('my_*');

		foreach($arr as $value){
			if(filectime($value)+$maxtime<time()){

				unlink($value);
			}

		}

	}

?>
