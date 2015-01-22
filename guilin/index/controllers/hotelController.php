<?php
/**
 * 酒店页
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-01
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class hotelController extends Y_Controller{
	//
	public function __construct(){
	}
	public function indexAction(){
		$web = Y_Db::init('seos');
		$str = Y::$controller.'_'.Y::$action;
		$web->where(array('name'=>$str));
		$web->field(array('title','author','keywords','des'));
		$this->assign('web',$web->find());
		$model = Y_Db::init('hotel');
		$where = array('status'=>1,'img!'=>'');
		$where = " status=1 AND img!=''";
		//地区
		if($area=$this->get('area')){
			$where .= "  AND area={$area}";	
		}
		//类型
		if($type=$this->get('type')){
			$where .= "  AND classes={$type}";		
		}
		//星级
		if($star=$this->get('star')){
			$where .= "  AND star={$star}";		
		}
		//价格
		if($price=$this->get('price')){
			$price = Ly_Items::getName('hotel','price',$price);
			if($price){
				$price = explode('-',$price);
				if(count($price)==1){
					$where .= " AND price{$price[0]}";
				}else{
					$where .= " AND price>={$price[0]} AND price<={$price[1]}";
				}
			}
		}
		$num = $model->where($where)->count();
		$model->field(array('hid','ishot','name','star','address','price','comment','mark','img','shares','classes'));
		$model->where($where);
		$sort = array('price','ishot','mark');
		$s=  $this->get('sort');
		if(!in_array($s,$sort))
			$s = 'ishot';
		$model->order("$s desc,hid desc");
		$page = $this->get('page')?intval($this->get('page')):1;
		$model->limit(($page-1)*15,15);
		$pages = new Y_Page($num,15);
		$this->assign('hotel_list',$model->select());
		$this->assign('hotel_totel',$num);
		$this->assign('hotel_pages',$pages->show());
		$this->display();
	}
	public function detailAction(){
		if( ($id=$this->get('id')) && is_numeric($id)){
			$model = Y_Db::init('hotel');
			$where = " hid={$id}";
			if($uid=Y_Session::get('uid')){
				//刚发表的，为审核的只有自己能看到
				$where .= " AND ( (status=1) OR (status<=1 AND uid={$uid}) )";
			}else{
				$where .= " AND status=1";
			}
			$model->where($where);
			$hinfo = $model->find();
			$web = array('title'=>Y_Pr::badStr($hinfo['name']),'keywords'=>Y_Pr::badStr($hinfo['name']),'des'=>Y_Pr::badStr($hinfo['des']));
			$this->assign('web',$web);
			$this->assign('hinfo',$hinfo);
			$this->display();
		}else{
			$this->error('您输入的地址有误，或者不存在！');
		}
		
	}
	public function orderAction(){
		if($hid=$this->get('id')){
			$model = Y_Db::init('hotel');
			$model->field(array('name','address','img','area','star','classes','contact','des'));
			$model->where(array('hid'=>$hid,'status'=>1));
			if($hotel=$model->find()){
					$room = Ly_Hotel_Rooms::getList($hid,true);
					if($room){
						$this->assign('rooms',$room);
						$this->assign('hotel',$hotel);
						$web = array('title'=>'[预订]'.$hotel['name'],'keywords'=>$hotel['name'],'des'=>Y_Pr::badStr($hotel['des']));
						$this->assign('web',$web);
						if($data['rid']=$this->post('rid')){
							$data['starttime'] = $this->post('starttime');
							$data['days'] = $this->post('days');
							$data['num'] = $this->post('num');
							$data['price'] = $this->post('price');
							$data['name'] = $this->post('name');
							$data['mobile'] = $this->post('mobile');
							$data['card'] = $this->post('card');
							$data['ordertime'] = time();
							$data['mark'] = $this->post('mark');
							$data['uid'] = Y_Session::get('uid');
							$data['hid'] = $this->get('id');
							$order = Y_Db::init('hotel_order');
							$v = md5('h'.$data['hid'].$data['uid'].$data['rid'].$data['num']);

							if(Y_Session::get($v) || $oid=$order->insert($data)){
								$data['oid'] = $oid?$oid:Y_Session::get($v);
								Y_Session::set($v,$data['oid']);
								$this->assign('order',$data);
								$this->display('orderok',false);
							}
						}else{
							
							$this->display(null,false);
						}
					}else{
						$this->error('该酒店暂时没有可以预定的房型！请稍后再访问！');
					}
			}else{
				$this->error('您预定的酒店不存在，或者已经被删除！');
			}
		}else{
			$this->error('链接地址有误！');
		}
		
	}
	public function commentListAction(){
		if($this->isAjax()){
			$id = $this->get('id');
			if($id){
				$model = Y_Db::init('hotel_comments');
				$model->field(array('id','uid','comments','pubtime','whole','health','device','service'));
				$where = array('status'=>0,'hid'=>$id);
				$model->where($where);
				$num = $model->count();
				$model->where($where);
				$page = $this->get('page')?intval($this->get('page')):1;
				$model->limit(($page-1)*10,10);
				$model->order('id desc');
				$pages = new Y_Page($num,10);
				$this->assign('comment',$model->select());
				$this->assign('pages',$pages->showAjax());
				$this->display(null,false);
			}
		}
		die;
	}
}
?>
