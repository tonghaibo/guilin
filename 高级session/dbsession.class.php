<?php
class dbsession{
	static $pdo;

	function __construct($pdo){
		self::$pdo=$pdo;

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

	static function open($path){
		return true;
	}

	static function close(){
		return true;
	}

	function read($sid){
		$sql="select data from session where id=?";

		$stmt=self::$pdo->prepare($sql);

		$stmt->execute(array($sid));

		$data=$stmt->fetch();
		return $data;


	}
	function write($sid,$data){
		if(!empty($data)){
			
			$sql='select sid,data,mtime from session where sid=?';
			$stmt=self::$pdo->prepare($sql);

			$stmt->execute(array($sid));


			//小于0就是没数据
			if($stmt->rowCount()<0){
				
				$d=$stmt->fetch(PDO::FETCH_ASSOC);

				if($d['mtime']+30<time()){
					$sql="update session set data=?,mtime=? where sid=?";
					
					$stmt=self::$pdo->prepare($sql);

					return $stmt->execute(array($data,time(),$sid));

				}



			}else{
			
				$sql="insert into session(sid,data,mtime) values(?,?,?)";

				$stmt=self::$pdo->prepare($sql);

				return $stmt->execute(array($sid,$data,time()));


			}

			
		
		
		
		}
		

	}

	function destroy($sid){
		$sql="delete from session where sid=?";

		$stmt=self::$pdo->prepare($sql);

		return $stmt->execute(array($sid));

		

	}
	function gc($maxtime){
		$sql="delete from session where mtime<?";

		$stmt=self::$pdo->prepare($sql);

		return $stmt->execute(array(time()-$maxtime));

	}

}
$pdo=new Pdo('mysql:host=localhost;dbname=38demo','root','liwenkaihaha');
$pdo->query('set names utf8');

$dbsession=new dbsession($pdo);
?>
