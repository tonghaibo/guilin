<?php
/**
 * Y框架数据库操作类
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-21
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
abstract class Y_Db{
	//驱动类型
	protected $dbType;
	//字段缓存目录
	//protected $cache_dir = P_DIR.'runtime/data/';
	//信息
	protected $msg = array();
	//字段
	protected $fields = array();
	//表名
	protected $tablename;
	//sql初始化
	protected $sql = array('field'=>'','where'=>'','order'=>'','limit'=>'','group'=>'');
	//获取表名
	public function __get($pro){
		if($pro=='tablename')
			return $this->tablename;
	}
	//重置sql
	protected function setNull(){
		$this->sql = array('field'=>'','where'=>'','order'=>'','limit'=>'','group'=>'');
	}
	public function count(){
		$where = '';
		$data=array();
		$args=func_get_args();
		if(count($args)>0){
			//有where条件的
			$where = $this->comWhere($args);
			$data = $where['data'];
			$where = $where['where'];
		//有where条件的
		}else if($this->sql['where']!=''){
			$where = $this->comWhere($this->sql['where']);
			$data= $where['data'];
			$where = $where['where'];
		}
		$sql = "SELECT COUNT(*) as count FROM {$this->tablename}{$where}";
		return $this->query($sql,__METHOD__,$data);
	}
	//组合连贯操作
	public function __call($method,$args){
		$method = strtolower($method);
		if(array_key_exists($method,$this->sql)){
			if(empty($args[0]) || (is_string($args[0]) && trim($args[0])==='')){
				$this->sql[$method] = '';
			}else{
				$this->sql[$method] = $args;
			}
			//limit特殊待遇
			if($method=='limit'){
				if($args[0]=='0')
					$this->sql[$method]=$args;
			}
			//field处理
			if($method=='field'){
				if(is_array($args[0])){
					$this->sql[$method]=implode(',',$args[0]);
				}else if(is_string($args[0])){
					$this->sql[$method]=$args[0];
				}
			}
		}else{
			Y_Debug::addMsg('[SQLERROR]-'.$method.'()不存在',3);
		}
		return $this;
	}
	//查询多条语句
	public function select(){
		$fields = $this->sql['field']!=''?$this->sql['field']:implode(',',$this->fields);
		$where = '';
		$data=array();
		$args=func_get_args();
		if(count($args)>0){
			$where = $this->comWhere($args);
		}else if($this->sql['where']!=''){
			$where = $this->comWhere($this->sql['where']);
		}else{
			$where['data'] = '';
			$where['where'] = ' where true';
		}
		
		$data = $where['data'];
		$where = $where['where'];
		$order = $this->sql['order']!=''?" ORDER BY {$this->sql['order'][0]}":" ORDER BY {$this->fields['pri']} ASC";
		$limit = $this->sql['limit']!=''?$this->comLimit($this->sql['limit']):'';
		$group = $this->sql['group']!=''?" GROUP BY {$this->sql['group'][0]}":'';
		$sql = "SELECT {$fields} FROM {$this->tablename}{$where}{$group}{$order}{$limit}";
		return $this->query($sql,__METHOD__,$data);
	}
	//获取一条数据
	public function find($pri=''){
		$fields = $this->sql["field"] != ""?$this->sql["field"]:implode(",", $this->fields);
		if($pri==""){
			$where= $this->comWhere($this->sql["where"]) ;
			$data=$where["data"];
			$where = $this->sql["where"] != "" ? $where["where"] : "";
		}else{
			$where=" where {$this->fields["pri"]}=?";  
			$data[]=$pri;
		}
		$order = $this->sql["order"] != "" ?  " ORDER BY {$this->sql["order"][0]}" : "";
		$sql="SELECT {$fields} FROM {$this->tablename}{$where}{$order} LIMIT 1";
  		return $this->query($sql,__METHOD__,$data);
	}
	//插入数据
	public function insert($data){
		if(empty($data)) return false;
		$sql = "INSERT INTO {$this->tablename}(".implode(',',array_keys($data)).") VALUES (".implode(',',array_fill(0,count($data),'?')).")";
		return $this->query($sql,__METHOD__,array_values($data));
	}
	//更新数据
	public function update($data){
		if(empty($data)) return false;
		if(is_array($data)){
			//主键是否存在
			if(array_key_exists($this->fields['pri'],$data)){
				$pri_value = $array[$this->fields['pri']];
				unset($data[$this->fields['pri']]);
			}
			$s = '';
			foreach($data as $k=>$v){
				$s .= "{$k}=?,";
				$datas[]=$v;
			}
			$s = rtrim($s,',');
			$setfield = $s;
		}else{
			$setfield=$data;
			$pri_value='';
		}
		$order = $this->sql["order"] != "" ?" ORDER BY {$this->sql["order"][0]}" : "";
		$limit = $this->sql["limit"] != "" ? $this->comLimit($this->sql["limit"]) : "";
		if($this->sql["where"] != ""){
			$where=$this->comWhere($this->sql["where"]);
			$sql="UPDATE  {$this->tablename} SET {$setfield}".$where["where"];
				if(!empty($where["data"])) {
					foreach($where["data"] as $v){
						$datas[]=$v; //value
					}
				}
				$sql.=$order.$limit;
		}else{
				
			$sql="UPDATE {$this->tablename} SET {$setfield}  WHERE {$this->fields["pri"]}=?";
			$datas[]=$pri_value; //value
		}
		return $this->query($sql,__METHOD__,$datas);
		
	}
	//删除操作
	public function delete(){
		$where="";
		$data=array();	
		$args=func_get_args();
		if(count($args)>0){
			$where = $this->comWhere($args);
			$data=$where["data"];
			$where= $where["where"];
		}else if($this->sql["where"] != ""){
			$where=$this->comWhere($this->sql["where"]);
			$data=$where["data"];
			$where=$where["where"];	
		}
		$order = $this->sql["order"] != "" ?  " ORDER BY {$this->sql["order"][0]}" : "";
		$limit = $this->sql["limit"] != "" ? $this->comLimit($this->sql["limit"]) : "";
			
		if($where=="" && $limit==""){
			$where=" where {$this->fields["pri"]}=''";
		}
		$sql="DELETE FROM {$this->tablename}{$where}{$order}{$limit}";		
		return $this->query($sql, __METHOD__,$data);
	}
	//组合
	private function comLimit($args){
		if(count($args)==2){
			return " LIMIT {$args[0]},{$args[1]}";
		}else if(count($args)==1){
			return " LIMIT {$args[0]}";
		}else{
			return '';
		}	
	}
	//组合SQL
	private function comWhere($args){
		$where = " WHERE ";
		$data=array();
		if(empty($args))
			return array('where'=>'','data'=>$data);
		foreach($args as $option){
			if(empty($option)){
				$where = '';
				continue;
			//字符串
			}else if(is_string($option)){
				//这里修改为$option,以前是$option[0]
				if(is_numeric($option[0])){
					$option =explode(',',$option);
					$where .= "{$this->fields['pri']} IN(".implode(',',array_fill(0,count($option),'?')).")";
					$data=$option;
					continue;
				}else{
					$where .= $option; //2
					continue;
				}
			//数字
			}else if(is_numeric($option)){
				$where .= "{$this->fields['pri']}=?";
				$data[0]=$option;
				continue;
			}else if(is_array($option)){
				if(isset($option[0])){
					$where .= "{$this->fields['pri']} IN(".implode(',',array_fill(0,count($option),'?')).")";
					$data=$option;
					continue;
				}
				foreach($option as $k=>$v){
					if(is_array($v)){
						$where .="{$k} IN(".implode(',',array_fill(0,count($v),'?')).")";
						foreach($v as $val){
							$data[]=$val;
						}	
					}else if(strpos($k,' ')){
						$where .= "{$k}?";
						$data[] = $v;
					}else if(isset($v[0]) && $v[0]=='%' && substr($v,-1)=='%'){
						$where .= "{$k} LIKE ?";
						$data[] = $v;
					}else{
						$where .= "{$k}=?";
						$data[] = $v;
					}
					$where .= " AND ";
				}
				$where = rtrim($where,'AND ');
				$where .= " OR ";
				continue;
			}
		}
		$where=rtrim($where,'OR ');
		return array('where'=>$where,'data'=>$data);
	}
	//调试sql
	protected function sql($sql,$params_arr){
		if(false ===strpos($sql,'?') || count($params_arr)==0)
			return $sql;
		if(false==strpos($sql,'%')){
			$sql = str_replace('?','%s',$sql);
			array_unshift($params_arr,$sql);
			return call_user_func_array('sprintf',$params_arr);
		}
	}
	protected function setMsg($mess){
		if(is_array($mess)){
			foreach($mess as $one){
				$this->msg[] = $one;
			}
		}else{
			$this->msg[] = $mess;
		}
	}
	protected function getMsg(){
		$str='';
		foreach($this->msg as $msg){
			$str .= $msg.'<br>';
		}
		return $str;
	}
	protected function escape_string_array($array){
		if(empty($array))
			return array();
		 $value=array();
		foreach($array as $val){
			$value[] = $val;
			//不去掉则插入单双号插不进
			 //$value[]=str_replace(array('"', "'"), '', $val);
		 }
		  return $value;
	}
	//联合查询传入model对象
	public function join(){
		$args = func_get_args();
		$baseFields = $this->sql['field']!=''?$this->sql['field']:implode(',',$this->fields);
		$baseFields = explode(',',$baseFields);
		$bf ='';
		foreach($baseFields as $b){
			$bf .= 'z.'.$b.',';
		}
		$baseFields = rtrim($bf,',');
		$where = '';
		$data=array();
		if($this->sql['where']!=''){
			$where = $this->comWhere($this->sql['where']);
		}
		$data = $where['data'];
		$where = $where['where'];
		if(is_object($args[0])){
			$join = '';
			$fields = '';
			foreach($args as $k=>$v){
				$cname = 'l'.($k+1);
				$join .= ' LEFT JOIN '.$v->tablename.' AS '.$cname;
				if($v->sql['where']){
					//注意这里的where必须为字符串的形式
					if(is_array($v->sql['where'][0])){
						list($k1,$v1) = each($v->sql['where'][0]);
						$join .= ' ON '.$cname.'.'.$k1.'=z.'.$v1;
					}else{
						$join .= ' ON '.$v->sql['where'][0];
					}
					
				}
				$vfield = $v->sql['field']?$v->sql['field']:$v->fields;
				$vfield = explode(',',$vfield);
				$vfs = ',';
				foreach($vfield as $vf){
					$vfs .= $cname.'.'.$vf.' AS '.$cname.'_'.$vf.',';
				}
				$fields .= ltrim($vfs,',');
			}
			$fields = rtrim($fields,',');
			$table = rtrim($table,',');
			$order = $this->sql["order"] != "" ?  " ORDER BY {$this->sql["order"][0]}" : "";
			$limit = $this->sql["limit"] != "" ? $this->comLimit($this->sql["limit"]) : "";
			$group = $this->sql['group']!=''?" GROUP BY {$this->sql['group'][0]}":'';
			$sql = 'SELECT '.$baseFields.','.$fields.' FROM '.$this->tablename.' AS z'.$join.$where.$group.$order.$limit;
			return $this->query($sql, __METHOD__,$data);
		}else{
			Y_Debug::addMsg('[SQLERROR]-join查询必须传入一个数据对象',3);
			return false;
		}
	}
	//子查询
	public function subquery(){
		$args = func_get_args();
		if(!is_object($args[0])){
			Y_Debug::addMsg('[SQLERROR]-subquery子查询，第一个参数必须为对象',3);
			return false;
		}
		//先获取查询
		$data=$this->select();
		$return = array();
		foreach($data as $k=>$v){
			foreach($args as $obj){
				if(!is_object($obj)){
					continue;
				}
				list($key,$val) = each($obj->sql['where'][0]);
				if($v[$val]){
					$zwhere = $key.'='.$v[$val];
				}else{
					continue;
				}
				$zdata = $obj->select($zwhere);
				//不判断了，四个关联字段一样的情况还是很少的
				$name = $key.'_'.$val;
				$v[$name] = $zdata;
				$return[$k] = $v;
			}
		}
		return $return;
	}
	abstract function query($sql,$method,$data=array());
	abstract function setTablename($tabname);
	//事务处理
	abstract function dbSize();
	abstract function dbVersion();
	//测试
	public static function init($className=null,$app=''){
		$db=null;
		$class = 'Y_Db_'.ucfirst(strtolower((Y::Config('db','driver'))));
		$db = new $class;
		if($className){
			$db->setTablename($className);
		}
		return $db;
	}
}
?>
