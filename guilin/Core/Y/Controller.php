<?php
/**
 * Y_Controller 总控制器
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')){
exit();
}
abstract class Y_Controller{
	//保存变量
	protected $_vars = array();
	//get等同与$_GET 默认是把html实体转换的
	public static function get($name,$html=true){
		if(isset($_GET[$name])){
		$name = trim($_GET[$name]);
		if(Y_MAGIC && Y_MAGIC==true){
			$name = self::addSlashes($name);	
		}
		if($html){
			$name = htmlspecialchars($name);
		}
		return $name;
		}
	}
	//Post等同与$_POST 默认是把html实体转换的
	public static function post($name,$html=true){
		if(isset($_POST[$name])){
		$name = trim($_POST[$name]);
		if(Y_MAGIC && Y_MAGIC==true){
			$name = self::addSlashes($name);	
		}
		if($html){
			$name = htmlspecialchars($name);
		}
		return $name;
		}

	}
	//过滤安全数据
	public static function filter($str,$html=true){
		$str = trim($str);
		if(Y_MAGIC && Y_MAGIC==true){
			$str = self::addSlashes($str);	
		}
		if($html){
			$str = htmlspecialchars($str);
		}
		return $str;
	}
	//带信息说明跳转
	public static function info($mess,$goto_url='',$type=null,$limit_time=5){
		if(!$mess) return false;
		if($goto_url==''){
			$goto_url = $_SERVER['HTTP_REFERER'];
		}
		//清除前面所有的
		ob_end_clean();
		ob_start();
		include(Y_DIR.'Views/message.php');
		//Y::loadFile(Y_DIR.'Views/message.php');
		$content = ob_get_clean();
		echo $content;
		exit();
	}
	//Ajax输出
	//@param array $data 返回数组
	//@param string $info 返回信息，默认为空
	//@param boolean $status 执行状态 1为true,0为fasle
	public static function ajaxReturn($info=null,$status=1,$data=array()){
		$result = array();
		$result['status'] = $status;
		$result['info'] = (!is_null($info))?$info:'';
		$result['data'] = $data;
		header('Content-Type:text/html;charset=utf-8');
		exit(json_encode($result));
	}
	//模版输出 新增缓存自控制
	protected  function display($name=null,$cache=true){
		$dir = A_DIR.'views/'.Y::$controller.'/';
		if(!is_dir($dir)){
			mkdir($dir);	
		}
		$name = $name?$name:Y::$action;
		$file = $dir.$name.'.html';
		self::createHta(dirname($file));
		//获取变量
		extract($this->_vars,EXTR_OVERWRITE);
		//加载模版文件
		include $file;
		if($cache){
			Y_Cache::set();
		}
	}
	//模版变量赋值
	public function assign($name,$value=''){
        	if(is_array($name)) {
            		$this->_vars   =  array_merge($this->tVar,$name);
        	}elseif(is_object($name)){
            		foreach($name as $key =>$val)
                	$this->_vars[$key] = $val;
        	}else {
            		$this->_vars[$name] = $value;
        	}
	}
	//是否是ajax提交
	protected function isAjax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}
	public function __set($name,$value) {
		$this->assign($name,$value);
	}
	public function __get($name) {
		return $this->_vars[$name];
	}
	//错误提示
	protected function error($mess,$url='',$time=5){
		$this->info($mess,$url,'error',$time);
	}
	//成功提示
	protected function success($mess,$url='',$time=5){
		$this->info($mess,$url,'ok',$time);
	}
	//跳转
	protected function Go($url){
		//参数分析.
		if (!$url) {
			return false;
		}		
		if (!headers_sent()) {
			header("Location:" . $url);			
		}else {
			echo '<script type="text/javascript">location.href="' . $url . '";</script>';
		}	
		exit();
	}
	/**
	 * stripslashes()的同功能操作
	 * 
	 * @access protected
	 * @param string $string 所要处理的变量
	 * @return mixed 变量
	 */
	protected static function delSlashes($string) {
		$string = trim($string);
		//参数分析.
		if (!$string) {
			return false;
		}			
		if (!is_array($string)) {
			return stripslashes($string);			
		}	
		foreach ($string as $key=>$value) {					
			$string[$key] = self::delSlashes($value);
		}			
		return $string;
	}	
	/**
	 * addslashes()的同功能操作
	 * 
	 * @access protected
	 * @params string $string 	所要处理的变量
	 * @return mixed			变量
	 */
	protected static function addSlashes($string) {
		$string = trim($string);
		//参数分析.
		if (!$string) {
			return false;
		}		
		if (!is_array($string)) {
			return addslashes($string);			
		}		
		foreach ($string as $key=>$value) {
			$string[$key] = self::addSlashes($value);
		}				
		return $string;
	}
	protected function getUrl($flag=false){
		$url = explode('/',$_SERVER['REQUEST_URI']);
		$url = array_pop($url);
		return $url?$url:'index.php';
	}
	//生产禁止访问模版的.htaccess文件
	protected function createHta($dir){
		$dir = rtrim($dir,'/').'/';
		$dir .='.htaccess';
		if(!file_exists($dir)){
			$content = 'Options -Indexes';
			$content .= "\r\nDeny from all";
			file_put_contents($dir,$content);
		}
	}
}
?>
