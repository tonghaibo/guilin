<?php
/**
 * 
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-21
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
class Y_Db_Mysqli extends Y_Db{
	static $mysqli = null;
	static function connect(){
		if(is_null(self::$mysqli)){
			//加载配置文件
			$mysqli = new mysqli(Y::Config('db','host'),Y::Config('db','user'),Y::Config('db','pass'),Y::Config('db','dbname'));
			if(mysqli_connect_errno()){
				Y_Debug::addMsg('数据库连接失败!');
				return false;
			}else{
				self::$mysqli=$mysqli;
				return $mysqli;
			}
		}else{
			return self::$mysqli;
		}
	}
	//查询
	public function query($sql,$method,$data=array()){
		$startTime = microtime(true);
		//清空
		$this->setNull();
		$value=$this->escape_string_array($data);
		$marr =explode('::',$method);
		$method = strtolower(array_pop($marr));
		if(strtolower($method)==trim('count')){
			$sql=preg_replace('/select.*?from/i','SELECT count(*) as count FROM',$sql);
		}
		$memkey=$this->sql($sql,$value);
		$mysqli=self::connect();
		if($mysqli)
			$stmt=$mysqli->prepare($sql);
		else
			return;
		//绑定参数
		if(count($value)>0){
			$s = str_repeat('s',count($value));
			array_unshift($value,$s);
			call_user_func_array(array($stmt,'bind_param'),$value);
		}
		if($stmt){
			$result = $stmt->execute();
		}
		if(!$result){
			Y_Debug::addMsg("SQL ERROR[{$mysqli->errno}] {$stmt->error}");
			Y_Debug::addMsg('清查看：'.$memkey);
			return;
		}
		$returnv=null;
		switch($method){
			case 'select':
				$stmt->store_result();
				$data=$this->getAll($stmt);
				$returnv=$data;
				break;
			case 'find':
				$stmt->store_result();
				if($stmt->num_rows>0){
					$data=$this->getOne($stmt);
					$retrunv=$data;
				}else{
					$retrunv = false;
				}
				break;
			case 'count':
				$stmt->store_result();
				$row=$this->getOne($stmt);
				$returnv=$row['count'];
				break;
			case 'insert':
				if($this->auto=='yes')
					$retrunv=$mysqli->insert_id();
				else
					$retrunv=$result;
				break;
			case 'delete':
			case 'update':
				$retruenv=$stmt->affected_rows;
			default:
				$returnv=$result;

		}
		$stopTime=microtime(true);
		$y=round(($stopTime-$startTime),4);
		Y_Debug::addMsg('[用时'.$y.'秒]-'.$memkey,2);
		return $returnv;
	}
	//获取多条
	private function getAll($stmt){
		$result = array();
		$field = $stmt->result_metadata()->fetch_fields();
		$out = array();
		//获取所有结果集中的字段名
		$fields = array();
		foreach ($field as $val) {
			$fields[] = &$out[$val->name];
		}
		//用所有字段名绑定到bind_result方上
		call_user_func_array(array($stmt,'bind_result'), $fields);
		while ($stmt->fetch()) {
			$t = array();  //一条记录关联数组
			foreach ($out as $key => $val) {
				$t[$key] = $val;
			}
			$result[] = $t;
		}
		return $result;  //二维数组
	}
	private function getOne($stmt) {
		$result = array();
		$field = $stmt->result_metadata()->fetch_fields();
		$out = array();
		//获取所有结果集中的字段名
		$fields = array();
		foreach ($field as $val) {
			$fields[] = &$out[$val->name];
		}
		//用所有字段名绑定到bind_result方上
		call_user_func_array(array($stmt,'bind_result'), $fields);
		$stmt->fetch();
		foreach ($out as $key => $val) {
			$result[$key] = $val;
		}
		return $result;  //一维关联数组
	}
	function setTablename($tabName){
			$cachefile=P_DIR."runtime/data/".$tabName.".php";
			$this->tablename=Y::Config('db','prefix').$tabName; //加前缀的表名
			if(file_exists($cachefile)){
				$json=ltrim(file_get_contents($cachefile),"<?ph ");
				$this->auto=substr($json,-3);
				$json=substr($json, 0, -3);
				$this->fields=(array)json_decode($json, true);	
			
			}else{
				$mysqli=self::connect();
				if($mysqli)
					$result=$mysqli->query("desc {$this->tablename}");
				else
					return;
			
				$fields=array();
				$auto="yno";
				while($row=$result->fetch_assoc()){
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
				if(!Y_DEBUG){
					file_put_contents($cachefile, "<?php ".json_encode($fields).$auto);
				}
				$this->fields=$fields;
				$this->auto=$auto;
				
			}
			Y_Debug::addmsg("表<b>{$this->tablename}</b>结构：".implode(",", $this->fields),2); //debug
	}
	public function beginTransaction() {
		self::connect()->autocommit(false);	
	}
	public function commit() {
		$mysqli=self::connect();
 		$mysqli->commit();
        	$mysqli->autocommit(true);
	}
	public function rollBack() {
		$mysqli=self::connect();
  		$mysqli->rollback();
        	$mysqli->autocommit(true);
	}
	public function dbSize() {
		$sql = "SHOW TABLE STATUS FROM " . DBNAME;
		if(defined("TABPREFIX")) {
			$sql .= " LIKE '".Y::Config('db','prefix')."%'";
		}
		$mysqli=self::connect();
		$result=$mysqli->query($sql);
		$size = 0;
		while($row=$result->fetch_assoc())
			$size += $row["Data_length"] + $row["Index_length"];
		return tosize($size);
	}
	function dbVersion() {
		$mysqli=self::connect();
		return $mysqli->server_info;
	}
}
?>
