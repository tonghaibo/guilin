<?php
/**
 * 线路
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-05
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class waylineController extends Y_Controller{
	//
	public function __construct(){
	}
	public function indexAction(){
		$web = Y_Db::init('seos');
		$str = Y::$controller.'_'.Y::$action;
		$web->where(array('name'=>$str));
		$web->field(array('title','author','keywords','des'));
		$this->assign('web',$web->find());
		$model = Y_Db::init('wayline');
		$where = "status=1";
		if($days=$this->get('days')){
			$where .= " AND days<={$days}";
		}
		if($tags=$this->get('tagid')){
			$ids = Ly_Tags_Bind::getIds($tags,'w');
			$d = '';
			if($ids){
				foreach($ids as $v){
					$d[] = $v['wid'];
				}
				$d = join(',',$d);
				$where .= " AND wid in($d)";
			}
		}
		if($s=$this->get('sText')){
			$where .= " AND name like '%{$s}%'";
		}
		if($s=$this->get('minPrice')){
			if(is_numeric($s)){
				$s = intval($s);
				$where .= " AND price>=$s";
			}
		}
		if($s=$this->get('maxPrice')){
			if(is_numeric($s)){
				$s = intval($s);
				$where .= " AND price<=$s";
			}
		}
		$num = $model->where($where)->count();
		$sort = $this->get('sort')?$this->get('sort'):'ishot';
		$model->order("$sort DESC,wid DESC");
		$page = $this->get('page')?intval($this->get('page')):1;
		$model->limit(($page-1)*10,10);
		$model->field(array('wid','uid','name','days','price','img','comment','mark','ishot','traffic','shares'));
		$model->where($where);
		$pages = new Y_Page($num,10);
		$this->assign('waylines',$model->select());
		$this->assign('pages',$pages->show());
		$this->display();
	}
	public function detailAction(){
		$web = array('title'=>'未找到该线路！','keywords'=>'','des'=>'');
		if($id=$this->get('id')){
			$model = Y_Db::init('wayline');
			$model->field(array('wid','uid','name','days','price','pricehalf','priceout','pricein','contact','img','traffic','comment','mark','ishot','notice','shares'));
			$model->where($id);
			$way = $model->find();
			$web = array('title'=>$way['name'],'keywords'=>$way['name'],'des'=>$way['name']);
			$this->assign('wayline',$way);
		}
		$this->assign('web',$web);
		$this->display();
	}
	public function orderAction(){
		$web = array('title'=>'未找到该线路','keywords'=>'','des'=>'');
		if($id=$this->get('id')){
			$model= Y_Db::init('wayline');
			$model->field(array('wid','uid','name','price','pricehalf','contact','img','traffic'));
			$model->where(array('status'=>1,'wid'=>$id));
			if($info = $model->find()){
				$this->assign('wayline',$info);
				$web = array('title'=>'[预定]'.$info['name'],'keywords'=>$info['name'],'des'=>$info['name']);
				unset($info);
				if($data['wid']=$this->post('wid')){
					$data['uid'] = Y_Session::get('uid');
					$data['name'] = $this->post('name');
					$data['adult'] = $this->post('adult');
					$data['child'] = $this->post('child');
					$data['price'] = $this->post('price');
					$data['ordertime'] = time();
					$data['card'] = $this->post('card');
					$data['child'] = $this->post('child');
					$data['mobile'] = $this->post('mobile');
					$data['starttime'] = $this->post('starttime');
					$order = Y_Db::init('wayline_order');
					$v = md5('w'.$data['wid'].$data['uid'].$data['adult'].$data['child']);
					if(Y_Session::get($v) || $oid=$order->insert($data)){
						$data['oid'] = $oid?$oid:Y_Session::get($v);
						Y_Session::set($v,$data['oid']);
						$this->assign('order',$data);
						$this->display('orderok',false);
					}
				}else{
					$this->assign('web',$web);
					$this->display(null,false);	
				}		
			}else{
				$this->error('您预定的线路不存在或已经被删除！');
			}
			
		}else{
			$this->error('访问地址有误！');
		}
		
	}
}
?>
