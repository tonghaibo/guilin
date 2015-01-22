<?php
/**
 * 后台主控制器
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-04
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class countController extends Y_Controller{
	public $admin;
	public $id;
	public function __construct(){
		$url = $_SERVER['REQUEST_URI'];
		$url = explode('?',$url);
		$url = array_pop($url);
		//已经登录的
		if($this->admin=Ly_Admin::isLogin()){
			$this->id = Ly_Points::getId();
			if($this->id){
				//提示
				Y_Log::write(array(Y_Session::get('G_name'),$this->id['name'],'GET{'.$url.'}'),'sys',0);
				if($this->admin['G_Power']['issuper']) return;
				if($this->id['ispublic']==1) return;
				if(in_array($this->id['id'],$this->admin['G_Power']['power'])) return;
				//警告记录
				Y_Log::write(array(Y_Session::get('G_name'),$this->id['name'],'GET{'.$url.'}'),'sys',1);
				$this->error('您无权访问该页面！');
			}else{
				//不存在的
				Y_Log::write(array(Y_Session::get('G_name'),'未知','GET{'.$url.'}'),'sys',2);
				$this->error('您访问的页面已经被删除，或者不存在！');
			}
		}else{
			//页面不存在
			Y_Log::write(array('未知','未登录访问','GET{'.$url.'}'),'sys',2);
			$this->Go('govern.php?ro=private&ac=login');
		}
	}
	public function indexAction(){
		$this->display('index');
	}
	//数据统计
	public function numAction(){
		
		$this->display();
	}
	//图像统计
	public function imgAction(){
		$this->display();
	}
	//当前在线
	public function onlineAction(){
		$this->display();
	}
}
?>
