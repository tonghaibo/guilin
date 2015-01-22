<?php

/**
 * PDO数据驱动
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-22
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class Y_Db_Pdo extends Y_Db{
	static $pdo=null;
	/**
	*获取数据库连接对象PDO
	*/
	static function connect(){
		if(is_null(self::$pdo)) {
			try{
				//如果有dsn
				if(Y::Config('db','dsm'))
					$dsn=DSN;
				else
					//默认mysql
				$dsn="mysql:host=".Y::Config('db','host').";dbname=".Y::Config('db','dbname');

				$pdo=new PDO($dsn, Y::Config('db','user'), Y::Config('db','pass'), array(PDO::ATTR_PERSISTENT=>true));
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$charset = Y::Config('db','charset')?Y::Config('db','charset'):'utf8';
				$pdo->query('set names '.Y::Config('db','charset'));
				self::$pdo=$pdo;
				return $pdo;
			}catch(PDOException $e){
				Y_Log::write('链接数据失败！','sql');
				die('<font color="red"><a href="mailto:yuexinok@126.com">Y框架提示</a>-数据库链接失败！</font>');
			}
		}else{
			return self::$pdo;
		}
	}
	function query($sql,$method,$data=array()){
		$startTime = microtime(true); 
		$this->setNull(); //初使化sql
		//这里修改
		$value=$this->escape_string_array($data);
		//$value = $data;
		$marr=explode("::", $method);
		$method=strtolower(array_pop($marr));
		if(strtolower($method)=='count'){
			$sql=preg_replace('/select.*?from/i','SELECT count(*) as count FROM',$sql);
		}
		$addcache=false;
		$memkey=$this->sql($sql, $value);
		try{
			$return=null;
	 		$pdo=self::connect();
			$stmt=$pdo->prepare($sql);  //准备好一个语句
			$result=$stmt->execute($value);   //执行一个准备好的语句
			switch($method){
				case "select":  //查所有满足条件的
				case 'join':
					$return=$stmt->fetchAll(PDO::FETCH_ASSOC);
					break;
				case "find":    //只要一条记录的
					$return=$stmt->fetch(PDO::FETCH_ASSOC);
					break;
				case "count":  //返回总记录数
					$row=$stmt->fetch(PDO::FETCH_NUM);					
					$return=$row[0];
					break;
				case "insert":  //插入数据 返回最后插入的ID
					if($this->auto=="yes")
						$return=$pdo->lastInsertId();
					else
						$return=$result;
					break;
				case "delete":
				case "update":        //update 
					$return=$stmt->rowCount();
					break;
				default:
					$return=$result;
			}
			$stopTime= microtime(true);
			$ys=round(($stopTime - $startTime) , 4);
			Y_Debug::addMsg('[<font color="red"> '.$ys.' </font>] - '.$memkey,2); //debug
			//慢查询
			if($ys>0.2){
				Y_Log::write('['.$ys.']'.$memkey,'sql',1);
			}
			return $return;
		}catch(PDOException $e){
			Y_Debug::addMsg($e->getMessage(),2);
			Y_Debug::addMsg("[BADSQL]：".$memkey,2); //debug
			//记录日志
			Y_Log::write('['.$memkey.'] - 报错！信息：'.$e->getMessage(),'sql');
		}	
	}

	/**
	 * 自动获取表结构
	 */ 
	function setTablename($tabName){
		$cachefile=P_DIR."runtime/data/".$tabName.".php";
		$this->tablename=Y::Config('db','prefix').$tabName; //加前缀的表名		
		if(!file_exists($cachefile)){
			try{
				$pdo=self::connect();
				$stmt=$pdo->prepare("desc {$this->tablename}");
				$stmt->execute();
				$auto="yno";
				$fields=array();
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
				if($row["Key"]=="PRI"){
					$fields["pri"]=strtolower($row["Field"]);
				}else{
					$fields[]=strtolower($row["Field"]);
				}
				if($row["Extra"]=="auto_increment")
						$auto="yes";
				}
				//如果表中没有主键，则将第一列当作主键
				if(!array_key_exists("pri", $fields)){
					$fields["pri"]=array_shift($fields);		
				}
				if(!Y_DEBUG)
					file_put_contents($cachefile, "<?php ".json_encode($fields).$auto);
				$this->fields=$fields;
				$this->auto=$auto;
			}catch(PDOException $e){
				Y_Debug::addMsg("[异常]：".$e->getMessage(),2);
				Y_Log::write('['.$tabName.']表不存在！','sql');
			}
		}else{
			$json=ltrim(file_get_contents($cachefile),"<?ph ");
			$this->auto=substr($json,-3);
			$json=substr($json, 0, -3);
			$this->fields=(array)json_decode($json, true);	
		}
		Y_Debug::addmsg("[<b>{$this->tablename}</b>]：".implode(",", $this->fields),2); //debug
	}
    	/**
	* 事务开始
    	*/
	public function beginTransaction() {
		$pdo=self::connect();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); 
		$pdo->beginTransaction();
	}	
	/**
     	* 事务提交
     	*/
	public function commit() {
		$pdo=self::connect();
		$pdo->commit();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); 
	}	
	/**
     	* 事务回滚
     	*/
	public function rollBack() {
		$pdo=self::connect();
		$pdo->rollBack();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); 
    	}
	/*
	 * 获取数据库使用大小
	 * @return	string		返回转换后单位的尺寸
	 */
	public function dbSize() {
		$sql = "SHOW TABLE STATUS FROM " . Y::Config('db','dbname');
		if(defined("TABPREFIX")) {
			$sql .= " LIKE '".Y::Config('db','prefix')."%'";
		}
		$pdo=self::connect();
		$stmt=$pdo->prepare($sql);  //准备好一个语句
		$stmt->execute();   //执行一个准备好的语句
		$size = 0;
		while($row=$stmt->fetch(PDO::FETCH_ASSOC))
			$size += $row["Data_length"] + $row["Index_length"];
		return Y_Pr::size($size);
	}
	public function listTable(){
		$sql = "SHOW TABLE STATUS FROM " . Y::Config('db','dbname');
		try{$pdo=self::connect();
		$stmt=$pdo->prepare($sql);  //准备好一个语句
		$stmt->execute();
		$i=0;
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$data[$i]['engine'] = $row['Engine'];
			$data[$i]['name'] = $row['Name'];
			$data[$i]['rows'] = $row['Rows'];
			$data[$i]['data_free'] = $row['Data_free'];
			$data[$i]['auto_increment'] = $row['Auto_increment'];
			$data[$i]['ctime'] = $row['Create_time'];
			$data[$i]['uptime'] = $row['Update_time'];
			$data[$i]['data_length'] = $row['Data_length'];
			$data[$i]['index_length'] = $row['Index_length'];
			$data[$i]['collation'] = $row['Collation'];
			$data[$i]['comment'] = $row['Comment'];
			$i++;
		}
		return $data;
		}catch(PDOException $e){
			Y_Debug::addMsg($e->getMessage(),2);
			Y_Debug::addMsg("[获取表信息".$this->tablename."]：失败",2); //debug
			//记录日志
			Y_Log::write('['.$this->tablename.'] - 获取表信息！信息：'.$e->getMessage(),'sql');
		}
	}
	public function descTable(){
		$sql = "desc ".$this->tablename;
		try{$pdo=self::connect();
		$stmt=$pdo->prepare($sql);  //准备好一个语句
		$stmt->execute();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$data[] = $row;
		}
		return $data;
		}catch(PDOException $e){
			Y_Debug::addMsg($e->getMessage(),2);
			Y_Debug::addMsg("[获取表结构".$this->tablename."]：失败",2); //debug
			//记录日志
			Y_Log::write('['.$this->tablename.'] - 获取表结构！信息：'.$e->getMessage(),'sql');
		}
	}
	public function indexTable(){
		$sql = "show index from  ".$this->tablename;
		try{$pdo=self::connect();
		$stmt=$pdo->prepare($sql);  //准备好一个语句
		$stmt->execute();
		$i=0;
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$data[$i]['colname'] = $row['Column_name'];
			$data[$i]['non_unique'] = $row['Non_unique'];
			$data[$i]['keyname'] = $row['Key_name'];
			$i++;
		}
		return $data;
		}catch(PDOException $e){
			Y_Debug::addMsg($e->getMessage(),2);
			Y_Debug::addMsg("[获取表索引".$this->tablename."]：失败",2); //debug
			//记录日志
			Y_Log::write('['.$this->tablename.'] - 获取索引失败！信息：'.$e->getMessage(),'sql');
		}
	}
	public function doTable($type){
		$type = strtolower($type);
		$do = array('check','analyze','optimize');
		if(in_array($type,$do)){
			try{$sql = $type.' table '.$this->tablename;
			$pdo = self::connect();
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
				$data[] = $row;
			}
			return $data;
			}catch(PDOException $e){
			Y_Debug::addMsg($e->getMessage(),2);
			Y_Debug::addMsg("[操作表".$this->tablename."]：".$type."失败",2); //debug
			//记录日志
			Y_Log::write('['.$this->tablename.'] - '.$type.'失败！信息：'.$e->getMessage(),'sql');
			}
		}
		return false;
	}
	//备份表
	public function exportTable(){
		$sql = "show create table ".$this->tablename;
		try{$pdo = self::connect();
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$h1 .= "-- 表名：`".$this->tablename."`\r\n";
		$h1 .=$row['Create Table'].";\r\n";
		
		$fields = array_values($this->fields);
		$fields_num = count($fields);
		$strlist = '';
		foreach($fields as $k){
			$strlist .= '`'.$k.'`,';
		}
		$sql = "SELECT ".rtrim($strlist,',')." FROM ".$this->tablename;
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$str = '';
		$j=0;
		while($row=$stmt->fetch(PDO::FETCH_NUM)){
			if($j%1000==0){
				//断行
				if($j!=0){
					$str = rtrim($str,",\r\n").";\r\n";
				}
				$str .= "INSERT INTO `".$this->tablename."`(".rtrim($strlist,',').") VALUES\r\n";
			}
			for($i=0;$i<$fields_num;$i++){
				if(empty($row[$i])){
					$row[$i] = 'NULL';
				}else if(!is_numeric($row[$i])){
					$row[$i] = "'".$row[$i]."'";
				}else{
					$row[$i] = $row[$i];
				}
			}
			$s = join(',',$row);
			$str .= '('.rtrim($s,',')."),\r\n";
			$j++;
		}
		//exit;
		$s = $str?(rtrim($str,",\r\n").";\r\n"):'';
		$h1 .= $s;
		return $h1;
		}catch(PDOException $e){
			Y_Debug::addMsg($e->getMessage(),2);
			Y_Debug::addMsg("[导出表".$this->tablename."]：失败",2); //debug
			//记录日志
			Y_Log::write('['.$this->tablename.'] - 导出失败！信息：'.$e->getMessage(),'sql');
		}
	}
	//列出说有表
	public function showTables(){
		try{$pdo = self::connect();
		$stmt = $pdo->prepare("show tables");
		$stmt->execute();
		while($row=$stmt->fetch(PDO::FETCH_NUM)){
			$data[] = $row[0];
		}
		return $data;
		}catch(PDOException $e){
			Y_Debug::addMsg($e->getMessage(),2);
			Y_Debug::addMsg("[列出所有表]：失败",2); //debug
			//记录日志
			Y_Log::write('[数据库] - 列出所有表失败！信息：'.$e->getMessage(),'sql');
		}
	}
	/*
	 * 数据库的版本
	 * @return	string		返回数据库系统的版本
	 */
	public function dbVersion() {
		$pdo=self::connect();
		return $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
	}
}
