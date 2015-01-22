<?php
/**
 * 地图
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-05
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class mapController extends Y_Controller{
	//
	public function __construct(){
		$web = Y_Db::init('seos');
		$str = Y::$controller.'_'.Y::$action;
		$web->where(array('name'=>$str));
		$web->field(array('title','author','keywords','des'));
		$this->assign('web',$web->find());
	}
	public function indexAction(){
		$this->display();
	}
	public function tagsAction(){
		$this->isAjax() or die;
		$tags = Y_Db::init('tags');
		$pid = $this->get('pid');
		if($pid){
			$where = "pid in($pid)";
		}else{
			$where = "pid!=0";
		}
		if($name=$this->get('name')){
			$where .= " name like '%{$name}%'";
		}
		$tags->where($where);
		$tags->field(array('id','name','lng','lat','pid'));
		$tags->order('sort desc');
		$tags = $tags->select();
		echo json_encode($tags);
		die;
	}
	public function viewAction(){
		$this->isAjax() or die;
		$id = $this->get('id');
		$model = Y_Db::init('tags');
		$model->where(array('id'=>$id));
		$info = $model->find();
		if($info){
			echo json_encode(array('error'=>0,'data'=>$info));
		}else{
			echo json_encode(array('error'=>1));
		}
		die;
	}
	public function loadAction(){
		$this->isAjax() or die;
		$type = $this->post('type');
		$t = array('c','w','h','u');
		if(!in_array($type,$t)) die('错误参数！');
		$id = intval($this->post('id'));
		$page = $this->get('page')?intval($this->get('page')):1;
		$model = Y_Db::init('tags_bind');
		$where['tid'] = $id;
		if($type=='c'){
			$where['cid!'] = 0;
			$model->where($where);
			$model->field('group_concat(cid) as c');
			$tags = $model->find();
			if($tags['c']){
				$model = Y_Db::init('content');
				$model->where("id in({$tags['c']}) AND status<=1");
				$num = $model->count();
				$model->where("id in({$tags['c']}) AND status<=1");
				$model->field(array('title','id','img','uid','pubtime','views','comments'));
				$model->limit(($page-1)*6,6);
				$model->order('ishot desc,id desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,6);
				$this->assign('pages',$pages->showAjax());
			}
			$this->display('content');
			die;
		}else if($type=='h'){
			$where['hid!'] = 0;
			$model->where($where);
			$model->field('group_concat(hid) as c');
			$tags = $model->find();
			if($tags['c']){
				$model = Y_Db::init('hotel');
				$model->where("hid in({$tags['c']}) AND status=1");
				$num = $model->count();
				$model->where("hid in({$tags['c']}) AND status=1");
				$model->field(array('name','classes','star','hid','img','address','area','price'));
				$model->limit(($page-1)*6,6);
				$model->order('ishot desc,hid desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,6);
				$this->assign('pages',$pages->showAjax());
			}
			$this->display('hotel');
			die;
		}else if($type=='w'){
			$where['wid!'] = 0;
			$model->where($where);
			$model->field('group_concat(wid) as c');
			$tags = $model->find();
			if($tags['c']){
				$model = Y_Db::init('wayline');
				$model->where("wid in({$tags['c']}) AND status=1");
				$num = $model->count();
				$model->where("wid in({$tags['c']}) AND status=1");
				$model->field(array('name','days','traffic','wid','img','price','pricehalf'));
				$model->limit(($page-1)*6,6);
				$model->order('ishot desc,wid desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,6);
				$this->assign('pages',$pages->showAjax());
			}
			$this->display('wayline');
			die;
		}else if($type=='u'){
			$where['uid!'] = 0;
			$model->where($where);
			$model->field('group_concat(uid) as c');
			$tags = $model->find();
			if($tags['c']){
				$model = Y_Db::init('user');
				$f='';
				if($uid=Y_Session::get('uid')){
					$f = Y_Db::init('friend');
					$f->field("group_concat(fuid) as u");
					$f->where(array('uid'=>$uid));
					$f = $f->find();
					$f = $f['u'];
					$f = $f?($f.','.$uid):$uid;
				}
				if($f){
					$model->where("uid in({$tags['c']}) AND status<=1 AND uid not in({$f}) AND sign!='' AND userimg!=''");
				}else{
					$model->where("uid in({$tags['c']}) AND status<=1 AND sign!='' AND userimg!=''");
				}
				$num = $model->count();
				if($f){
					$model->where("uid in({$tags['c']}) AND status<=1 AND uid not in({$f}) AND sign!='' AND userimg!=''");
				}else{
					$model->where("uid in({$tags['c']}) AND status<=1  AND sign!='' AND userimg!=''");
				}
				$model->field(array('name','userimg','uid','userimgflag','ranklevel','attention','follow','sign'));
				$model->limit(($page-1)*6,6);
				$model->order('uid desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,6);
				$this->assign('pages',$pages->showAjax());
			}
			$this->display('user');
			die;
		}
	}
	//地图加载
	public function loadhtmlAction(){
		if($hid=$this->get('hid')){
			$model = Y_Db::init('hotel');
			$model->field(array('img','name','price','lng','lat','contact','hid','area','address'));
			$model->where(array('hid'=>$hid,'status'=>1));
			$this->assign('info',$model->find());
			$this->display('loadhtml');
		}
		die();
	}
}
?>
