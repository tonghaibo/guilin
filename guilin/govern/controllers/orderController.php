<?php
/**
 * 订单管理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-14
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class orderController extends Y_Controller{
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
		$this->display();
	}
	public function hotelAction(){
		$method=$this->get('method');
		if($method=='pass'){
			$id = $this->get('id');
			$data['status'] = $this->get('v');
			$model = Y_Db::init('hotel_order');
			if($id){
				$model->where($id)->update($data);
				$this->success('更新成功！');
			}else{
				$this->error('更新失败！');
			}	
		}else if($method=='view'){
			$model = Y_Db::init('hotel_order');
			if($info=$model->find($this->get('id'))){
				$this->assign('info',$info);
				$this->display('hotel/view');
			}else{
				$this->error('传入的订单号有误！');
			}
			die;
		}
		$where = 'true';
		if($uid = $this->get('s_uid')){
			$where .= " AND uid={$uid}";
		}
		if($hid = $this->get('s_hid')){
			$where .= " AND hid={$hid}";
		}
		if($mindate = $this->get('s_mindate')){
			$mindate = strtotime($mindate);
			$where .= " AND ordertime>={$mindate}";
		}
		if($maxdate = $this->get('s_maxdate')){
			$maxdate = strtotime($maxdate);
			$where .= " AND ordertime>={$maxdate}";
		}
		if($status=$_GET['status'] and $status!=''){
			$status = join(',',$status);
			$where .= " AND status in($status)";
		}
		if($id=$this->get('s_id')){
			$where = "id={$id}";
		}
		$model = Y_Db::init('hotel_order');

		$page = $this->get('page')?intval($this->get('page')):1;
		$num = $model->where($where)->count();
		$this->assign('list',$model->where($where)->order('id DESC')->limit(($page-1)*10,10)->select());
		$pages = new Y_Page($num,10);
		$this->assign('pages',$pages->show());
		$this->display();
	}
	public function waylineAction(){
		$method=$this->get('method');
		if($method=='pass'){
			$id = $this->get('id');
			$data['status'] = $this->get('v');
			$model = Y_Db::init('wayline_order');
			if($id){
				$model->where($id)->update($data);
				$this->success('更新成功！');
			}else{
				$this->error('更新失败！');
			}	
		}else if($method=='view'){
			$model = Y_Db::init('wayline_order');
			if($info=$model->find($this->get('id'))){
				$this->assign('info',$info);
				$this->display('wayline/view');
			}else{
				$this->error('传入的订单号有误！');
			}
			die;
		}
		$where = 'true';
		if($uid = $this->get('s_uid')){
			$where .= " AND uid={$uid}";
		}
		if($wid = $this->get('s_wid')){
			$where .= " AND wid={$wid}";
		}
		if($mindate = $this->get('s_mindate')){
			$mindate = strtotime($mindate);
			$where .= " AND ordertime>={$mindate}";
		}
		if($maxdate = $this->get('s_maxdate')){
			$maxdate = strtotime($maxdate);
			$where .= " AND ordertime>={$maxdate}";
		}
		if($status=$_GET['status'] and $status!=''){
			$status = join(',',$status);
			$where .= " AND status in($status)";
		}
		if($id=$this->get('s_id')){
			$where = "id={$id}";
		}
		$model = Y_Db::init('wayline_order');

		$page = $this->get('page')?intval($this->get('page')):1;
		$num = $model->where($where)->count();
		$this->assign('list',$model->where($where)->order('id DESC')->limit(($page-1)*10,10)->select());
		$pages = new Y_Page($num,10);
		$this->assign('pages',$pages->show());
		$this->display();
	}
}
?>
