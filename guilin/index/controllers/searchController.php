<?php
/**
 * 搜索控制器
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-12-19
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class searchController extends Y_Controller{
	public function indexAction(){
		die('参数错误');
	}
	public function userAction(){
		$web = array('title'=>'搜素用户','keywords'=>'','des'=>'');
		$this->assign('web',$web);
		$model = Y_Db::init('user');
		$where = "status<=1";
		if($s=$this->get('sText')){
			$where .= " AND name like '%{$s}%'";
		}
		if($s=intval($this->get('sex'))){
			$where .= " AND sex={$s}";
		}
		if($s=intval($this->get('cityid'))){
			$where .= " AND provinceid={$s}";
		}
		$page = $this->get('page')?intval($this->get('page')):1;
		$num = $model->where($where)->count();
		$model->where($where);
		$model->field(array('uid','name','userimg','userimgflag','sex','ranklevel','provinceid','cityid','follow','attention','shares','views','sign'));
		$model->order('uid desc');
		$model->limit(($page-1)*5,5);
		$this->assign('info',$model->select());
		$pages = new Y_Page($num,5);
		$this->assign('pages',$pages->show());
		$this->assign('snum',$num);
		$this->display('user');	
	}
	public function hotelAction(){
		$web = array('title'=>'搜素酒店','keywords'=>'','des'=>'');
		$this->assign('web',$web);
		$model = Y_Db::init('hotel');
		$where = 'status=1';
		if($s=$this->get('sText')){
			$where .= " AND name like '%{$s}%'";
		}
		if($s=$this->get('star')){
			if(is_numeric($s)){
				$s=intval($s);
				$where .= " AND star={$s}";
			}
		}
		if($s=$this->get('area')){
			if(is_numeric($s)){
				$s=intval($s);
				$where .= " AND area={$s}";
			}
		}
		if($s=$this->get('classes')){
			if(is_numeric($s)){
				$s=intval($s);
				$where .= " AND classes={$s}";
			}
		}
		if($s=$this->get('minPrice')){
			if(is_numeric($s)){
				$s=intval($s);
				$where .= " AND price>={$s}";
			}
		}
		if($s=$this->get('maxPrice')){
			if(is_numeric($s)){
				$s=intval($s);
				$where .= " AND price<={$s}";
			}
		}
		$num = $model->where($where)->count();
		$page = $this->get('page')?intval($this->get('page')):1;
		$model->where($where);
		$model->field(array('hid','name','img','area','address','classes','price','star','contact'));
		$model->order("ishot desc,hid desc");
		$model->limit(($page-1)*12,12);
		$this->assign('info',$model->select());
		$pages = new Y_Page($num,12);
		$this->assign('sNum',$num);
		$this->assign('pages',$pages->show());
		$this->display('hotel');
	}
}
?>
