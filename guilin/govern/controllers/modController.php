<?php
/**
 * 内容模块
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-24
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die();
class modController extends Y_Controller{
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
	//标签
	public function tagAction(){
		$method = $this->get('method');
		if($method=='edit'){
			$id = $this->get('id');
			if($info=Ly_Tags::getInfo($id)){
				if($data['name']=$this->post('name')){
					$data['pid'] = $this->post('pid');
					$data['sort'] = $this->post('sort');
					$data['lng'] = floatval($this->post('lng'));
					$data['lat'] = floatval($this->post('lat'));
					$data['des'] = $this->post('des');
					$data['img'] = Y_Upload::upfile('img','public/uploads/tags/');
					//die;
					if(Ly_Tags::update($id,$data)){
						$this->success('编辑标签成功！');
					}else{
						$this->error('编辑标签失败！');
					}
				}
				$this->assign('tlist',Ly_Tags::getList(0));
				$this->assign('tinfo',$info);
				$this->display('tag/edit');
			}else{
				$this->error('传入ID有误！');
			}
			
		}else if($method=='add'){
			if($data['name']=$this->post('name')){
				$data['pid'] = $this->post('pid');
				$data['sort'] = $this->post('sort');
				$data['lng'] = floatval($this->post('lng'));
				$data['lat'] = floatval($this->post('lat'));
				$data['des'] = $this->post('des');
				$data['img'] = Y_Upload::upfile('img','public/uploads/tags/');
				if(Ly_Tags::insert($data)){
					$this->success('添加新标签成功！');
				}else{
					$this->error('添加新标签失败！');
				}
			}
			$this->assign('tlist',Ly_Tags::getList(0));
			$this->display('tag/add');
		}else if($method=='del'){
			//删除
			if($this->isAjax()){
				$id = $this->get('id');
				if(Ly_Tags::delete($id)){
					echo 1;
				}
				die;
			}
		}else if($method=='bind'){
			//提交
			if($this->post('bt')){
				$id = $this->get('id');
				$t = $this->get('t');
				if($t=='w'){
					$data['wid'] = $id;
				}else if($t=='h'){
					$data['hid'] = $id;
				}else if($t=='c'){
					$data['cid'] = $id;
				}else{
					$this->error('非法值传入！');
				}
				//先删除
				Ly_Tags_Bind::delete($data);
				$tags = $_POST['tids'];
				$tags = array_unique($tags);
				if($tags){
					foreach($tags as $val){
						$data['tid'] = $val;
						Ly_Tags_Bind::insert($data);
					}
				}
				$this->success('添加标签成功！');
				die;
			}
			$id = $this->get('id');
			$t = $this->get('t');
			if($t=='w'){
				$data['wid'] = $id;
				$info['type'] = '线路';
				$info['name'] = Ly_Wayline::getName($id);
				$info['id'] = $id;
			}else if($t=='h'){
				$data['hid'] = $id;
				$info['type'] = '酒店';
				$info['name'] = Ly_Hotel::getName($id);
				$info['id'] = $id;
			}else if($t=='c'){
				$data['cid'] = $id;
				$info['type'] = '文章';
				$info['name'] = Ly_Content::getTitle($id);
				$info['id'] = $id;
			}else{
				$this->error('无效参数！');
			}
			if($info){
				$this->assign('info',$info);
				$this->assign('tags',Ly_Tags_Bind::getTags($data));
				$this->display('tag/bind');
			}else{
				$this->error('传入参数有误！');
			}
			
		}else if($method=='getTags'){
			if($this->isAjax()){
				$id = $this->get('id');
				$t = $this->get('t');
				if($t=='w'){
					$data['wid'] = $id;
				}else if($t=='h'){
					$data['hid'] = $id;
				}else if($t=='c'){
					$data['cid'] = $id;
				}else{
					die;
				}
				$d = Ly_Tags_Bind::getTags($data);
				if($d){
					echo json_encode($d);
				}
				die;
			}
		
		}else{
			//展示子类
			if($this->isAjax()){
				$pid = $this->post('pid');
				if($pid==0){
					die;
				}
				$model = Y_Db::init('tags');
				$name = $this->post('name');
				$page = $this->post('page');
				$page = intval($page);
				$where = 'true';
				if($name){
					$where .= " and name like '%$name%' ";
				}
				if($pid){
					$where .= " and pid=$pid ";
				}
				$data = $model->where($where)->order('sort DESC,id DESC')->limit(10*$page,10)->select();
				$num = $model->where($where)->count();
				$num = ceil($num/10);
				$arr['data'] = $data;
				$arr['page'] = $page;
				$arr['total'] = $num;
				echo json_encode($arr);
				die;
			}
			$this->assign('tlist',Ly_Tags::getList(0));
			$this->display('tag');
		}
	}
	//酒店管理
	public function hotelAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['name']=$this->post('name')){
				$data['classes'] = $this->post('classes');
				$data['des'] = $this->post('des');
				if($_POST['img']){
					$data['img'] = $this->post('img');
				}
				$data['address'] = $this->post('address');
				if($_POST['lng']){
					$data['lng'] = $this->post('lng');
					$data['lat'] = $this->post('lat');
				}
				$data['price'] = $this->post('price');
				$data['traffic'] = $this->post('traffic');
				if($_POST['ctime']){
					$data['ctime'] = $this->post('ctime');
				}
				$data['contact'] = $this->post('contact');
				if($_POST['servers']){
					$data['servers'] = join($_POST['servers'],',');
				}
				if($_POST['relaxation']){
					$data['relaxation'] = join($_POST['relaxation'],',');
				}
				if($_POST['notice']){
					$data['notice'] = $this->post('notice');
				}
				if($_POST['portraiture']){
					$data['portraiture'] = $this->post('portraiture');
				}
				if($_POST['uploads']){
					$data['uploads'] = join($_POST['uploads'],',');
				}
				if($_POST['dc']){
					$data['dc'] = join($_POST['dc'],',');
				}
				$data['area'] = $this->post('area');
				$data['star'] = $this->post('star');
				$data['ishot'] = $this->post('ishot');
				$data['pubtime'] = time();
				if(Ly_Hotel::insert($data)){
					$this->success('添加酒店成功！');
				}else{
					$this->error('添加酒店失败！');
				}
			}else{
				$this->assign('clist',Ly_Items::getList('hotel','classes'));
				$this->display('hotel/add');
			}
		}else if($method=='edit'){
			$hid = $this->get('hid');
			if($info=Ly_Hotel::getInfo($hid)){
				if($data['name']=$this->post('name')){
					$data['classes'] = $this->post('classes');
				$data['des'] = $this->post('des');
				if($_POST['img']){
					$data['img'] = $this->post('img');
				}
				$data['address'] = $this->post('address');
				if($_POST['lng']){
					$data['lng'] = $this->post('lng');
					$data['lat'] = $this->post('lat');
				}
				$data['price'] = $this->post('price');
				$data['traffic'] = $this->post('traffic');
				if($_POST['ctime']){
					$data['ctime'] = $this->post('ctime');
				}
				$data['contact'] = $this->post('contact');
				if($_POST['servers']){
					$data['servers'] = join($_POST['servers'],',');
				}
				if($_POST['relaxation']){
					$data['relaxation'] = join($_POST['relaxation'],',');
				}
				if($_POST['notice']){
					$data['notice'] = $this->post('notice');
				}
				if($_POST['portraiture']){
					$data['portraiture'] = $this->post('portraiture');
				}
				if($_POST['uploads']){
					$data['uploads'] = join($_POST['uploads'],',');
				}
				if($_POST['dc']){
					$data['dc'] = join($_POST['dc'],',');
				}
				$data['ishot'] = $this->post('ishot');
				$data['area'] = $this->post('area');
				$data['star'] = $this->post('star');
				if(Ly_Hotel::update($hid,$data)){
					$this->success('修改酒店成功！');
				}else{
					$this->error('修改酒店失败！');
				}
				}else{
					$this->assign('hinfo',$info);
					$this->assign('clist',Ly_Items::getList('hotel','classes'));
					$this->display('hotel/edit');
				}
			}else{
				$this->error('传入酒店ID无效！');
			}
		}else if($method=='pass'){
			if($hid=$_GET['hid']){
				$data['status'] = intval($this->get('v'));
				$model = Y_Db::init('hotel');
				$model->where($hid);
				if($model->update($data)){
					$this->success('操作成功！');
				}else{
					$this->error('操作失败！');
				}
			}else{
				$this->error('请传入ID值！');
			}
		}else if($method=='upuser'){
			if($this->isAjax()){
				$s = $this->get('s');
				$d = $this->post('d');
				if($s=='e'){
					if(is_numeric($d)){
						if(Ly_User::getEmail($d)){
							echo $d;
						}	
					}else{
						echo Ly_User::getUid($d);
					}
					
				}else if($s=='u'){
					if($email=Ly_User::getEmail($d)){
						$data['uid'] = $d;
						$id = $this->get('hid');
						Ly_Hotel::update($id,$data);
						echo $email;
					}
				}	
			}
			die;
		}else if($method=='hot'){
			if($hid=$this->get('hid')){
				$data['ishot'] = intval($this->get('hot'));
				if(Ly_Hotel::update($hid,$data)){
					$this->success('操作成功！');
				}else{
					$this->error('操作失败！');
				}
			}
		}else if($method=='view'){
			//查看详情
			$wid = $this->get('id');
			if($wid){
				$model = Y_Db::init('hotel');
				$info = $model->find($wid);
				if($info){
					$this->assign('info',$info);
					$this->display('hotel/view');
				}else{
					$this->error('酒店ID传入有误！');
				}
			}else{
				$this->error('请传入酒店ID');
			}
			die;
		
		}else if($method=='bed'){
			$hid=$this->get('hid');
			if($hotel=Ly_Hotel::getBase($hid)){
				$act = $this->get('act');
				if($act=='add'){
					$data['hid'] = $hid;
					$data['name'] = $this->post('name');
					$data['price'] = floatval($this->post('price'));
					$data['bed'] = intval($this->post('bed'));
					$data['broadband'] = intval($this->post('broadband'));
					$data['breakfast'] = intval($this->post('breakfast'));
					$data['payment'] = intval($this->post('payment'));
					$data['status'] = intval($this->post('status'));
					$data['area'] = floatval($this->post('area'));
					$data['isfull'] = intval($this->post('isfull'));
					if($_POST['img']){
						$data['img'] = $this->post('img');
					}
					if($rid=$this->get('rid')){
						if(Ly_Hotel_Rooms::update($rid,$data)){
							echo 1;
						}
					}else{
						if(Ly_Hotel_Rooms::insert($data)){
							echo 1;
						}	
					}
					die;
				}else{
					if($rlist=Ly_Hotel_Rooms::getList($hid)){
						$this->assign('rlist',$rlist);
					}
					$this->assign('hotel',$hotel);
					$this->display('hotel/bed');
				}
			}else{
				$this->error('酒店ID传入有误！');
			}
		}else{
			$where = 'true';
			//name
			if($name=$this->get('s_name')){
				$where .= " AND name like '%{$name}%' ";
			}
			//classes
			if($classes=$this->get('s_classes')){
				$where .= " AND  classes='{$classes}'";
			}
			//status
			if(isset($_GET['s_status'])){
				$status = join($_GET['s_status'],',');
				$where .= " AND status in({$status})";
			}
			//ishot
			if($hot=$this->get('s_ishot')){
				$where .= " AND ishot='{$hot}'";
			}
			//puttime
			if($t1=$this->get('s_pubtime1')){
				$t1 = strtotime($t1);
				$where .= " AND pubtime>='{$t1}'";
			}
			if($t2=$this->get('s_pubtime2')){
				$t2 = strtotime($t2);
				$where .= " AND pubtime<='{$t2}'";
			}
			//uid
			if($uid=$this->get('s_uid')){
				if(!is_numeric($uid)){
					$uid = Ly_User::getUid($uid);
				}
				if($uid){
					$where .= " AND uid='{$uid}'";
				}
			}
			//ID
			if($hid=$this->get('s_hid')){
				$where = "hid='{$hid}'";
			}
			$page = intval($this->get('page'));
			$page = $page?$page:1;
			$model = Y_Db::init('hotel');
			$model->where($where);
			$count = $model->count();
			$model->field(array('hid','uid','ishot','name','classes','img','address','price','contact','pubtime','status'));
			$model->order('status ASC,hid DESC,ishot DESC');
			$model->limit(($page-1)*10,10);
			$model->where($where);
			$this->assign('hlist',$model->select());
			$page = new Y_Page($count,10);
			$this->assign('pages',$page->show());
			$this->display('hotel');
		}
	}
	//线路
	public function waylineAction(){
		$method=$this->get('method');
		if($method=='add'){
			if($data['name'] = $this->post('name')){
				$data['img'] = floatval($this->post('img'));
				$data['days'] = intval($this->post('days'));
				$data['price'] = floatval($this->post('price'));
				$data['pricehalf'] = floatval($this->post('pricehalf'));
				$data['pricein'] = $this->post('pricein');
				$data['priceout'] = $this->post('priceout');
				$data['traffic'] = $this->post('traffic');
				$data['contact'] = $this->post('contact');
				$data['notice'] = $this->post('notice');
				$data['ishot'] = $this->post('ishot');
				if($this->post('name1')){
					$data['pubtime'] = time();
					if($wid=Ly_Wayline::insert($data)){
						for($i=1;$i<=$data['days'];$i++){
							$days['wid'] = $wid;
							$days['name'] = $this->post('name'.$i);
							$days['contents'] = $this->post('contents'.$i);
							$days['live'] = $this->post('live'.$i);
							$days['eat1'] = $this->post('eat1'.$i)?1:0;
							$days['eat2'] = $this->post('eat2'.$i)?1:0;
							$days['eat3'] = $this->post('eat3'.$i)?1:0;
							$days['daynum'] = $i;
							Ly_Wayline_Days::insert($days);
							unset($days);
						}
						$this->success('添加线路成功！');
					}else{
						$this->error('添加线路失败！');
					}
				}else{
					$this->assign('hidata',$data);
					$this->display('wayline/addays');
				}
				
			}else{
				$this->display('wayline/add');
			}
		}else if($method=='edit'){
			$wid = $this->get('wid');
			if($winfo=Ly_Wayline::getInfo($wid)){
				//如果是线路修改
				if($this->get('act')=='days'){
					
					if($this->post('name1')){
						for($i=1;$i<=$winfo['days'];$i++){
							$days['wid'] = $wid;
							$days['name'] = $this->post('name'.$i);
							$days['contents'] = $this->post('contents'.$i);
							$days['live'] = $this->post('live'.$i);
							$days['eat1'] = $this->post('eat1'.$i)?1:0;
							$days['eat2'] = $this->post('eat2'.$i)?1:0;
							$days['eat3'] = $this->post('eat3'.$i)?1:0;
							if($d=$this->post('d'.$i)){
								Ly_Wayline_Days::update($wid,$d,$days);
							}else{
								$days['daynum'] = $i;
								Ly_Wayline_Days::insert($days);
							}
							unset($days);
						}
						$this->success('修改线路成功');
					}else{
						$this->assign('winfo',$winfo);
						$this->display('wayline/editdays');
					}
				}else{
					//修改
					if($data['name']=$this->post('name')){
						$data['img'] = floatval($this->post('img'));
						$data['days'] = intval($this->post('days'));
						$data['price'] = floatval($this->post('price'));
						$data['pricehalf'] = floatval($this->post('pricehalf'));
						$data['pricein'] = $this->post('pricein');
						$data['priceout'] = $this->post('priceout');
						$data['traffic'] = $this->post('traffic');
						$data['contact'] = $this->post('contact');
						$data['notice'] = $this->post('notice');
						$data['ishot'] = $this->post('ishot');
						if(Ly_Wayline::update($wid,$data)){
							//删除多余的
							Ly_Wayline_Days::delete($wid,$data['days'],true);
							$this->success('修改路线成功！');
						}else{
							$this->error('修改线路失败！');
						}	
					}else{
						$this->assign('winfo',$winfo);
						$this->display('wayline/edit');
					}
				}
			}else{
				$this->error('线路传入ID有误！');
			}
		}else if($method=='pass'){
			if($id = $this->get('wid')){
				$data['status'] = intval($this->get('v'));
				$model = Y_Db::init('wayline');
				$model->where($id);
				if($model->update($data)){
					$this->success('更新线路状态成功！');
				}else{
					$this->error('更新线路状态失败！');
				}
			}else{
				$this->error('请传入ID值！');
			}	
		}else if($method=='hot'){
			if($hid=$this->get('wid')){
				$data['ishot'] = intval($this->get('hot'));
				if(Ly_Wayline::update($hid,$data)){
					$this->success('操作成功！');
				}else{
					$this->error('操作失败！');
				}
			}
		}else if($method=='upuser'){
			if($this->isAjax()){
				$s = $this->get('s');
				$d = $this->post('d');
				if($s=='e'){
					if(is_numeric($d)){
						if(Ly_User::getEmail($d)){
							echo $d;
						}	
					}else{
						echo Ly_User::getUid($d);
					}
					
				}else if($s=='u'){
					if($email=Ly_User::getEmail($d)){
						$data['uid'] = $d;
						$id = $this->get('wid');
						Ly_Wayline::update($id,$data);
						echo $email;
					}
				}	
			}
			die;
		}else if($method=='view'){
			//查看详情
			$wid = $this->get('id');
			if($wid){
				$model = Y_Db::init('wayline');
				$info = $model->find($wid);
				if($info){
					$this->assign('info',$info);
					$this->display('wayline/view');
				}else{
					$this->error('线路ID传入有误！');
				}
			}else{
				$this->error('请传入线路ID');
			}
			die;
		
		}else{
			$where = 'true';
			//name
			if($name=$this->get('s_name')){
				$where .= " AND name like '%{$name}%' ";
			}
			//status
			if(isset($_GET['s_status'])){
				$status = join($_GET['s_status'],',');
				$where .= " AND status in({$status})";
			}
			//ishot
			if($hot=$this->get('s_ishot')){
				$where .= " AND ishot='{$hot}'";
			}
			//puttime
			if($t1=$this->get('s_pubtime1')){
				$t1 = strtotime($t1);
				$where .= " AND pubtime>='{$t1}'";
			}
			if($t2=$this->get('s_pubtime2')){
				$t2 = strtotime($t2);
				$where .= " AND pubtime<='{$t2}'";
			}
			//uid
			if($uid=$this->get('s_uid')){
				if(!is_numeric($uid)){
					$uid = Ly_User::getUid($uid);
				}
				if($uid){
					$where .= " AND uid='{$uid}'";
				}
			}
			//ID
			if($id=$this->get('s_wid')){
				$where = "wid={$id}";
			}
			$page = intval($this->get('page'));
			$page = $page?$page:1;
			$model = Y_Db::init('wayline');
			$model->where($where);
			$count = $model->count();
			$model->field(array('wid','uid','ishot','name','img','price','pricehalf','contact','pubtime','status','days'));
			$model->order('status ASC,wid DESC,ishot DESC');
			$model->limit(($page-1)*10,10);
			$model->where($where);
			$this->assign('wlist',$model->select());
			$page = new Y_Page($count,10);
			$this->assign('pages',$page->show());
			$this->display('wayline');
		}
	}
	//文章管理
	public function contentAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['title']=$this->post('title')){
				$data['tid'] = $this->post('tid');
				$data['address'] = $this->post('address');
				$data['lng'] = floatval($this->post('lng'));
				$data['lat'] = floatval($this->post('lat'));
				$data['dates'] = $this->post('dates');
				$data['content'] = $this->post('content',false);
				$data['uploads'] = join(',',$_POST['uploads']);
				$data['img'] = $this->post('img');
				$data['ishot'] = $this->post('ishot');
				if(Ly_Content::insert($data)){
					$this->success('添加文章成功！');
				}else{
					$this->error('添加文章失败！');
				}
			}
			$this->assign('tags',Ly_Tags::getList(0));
			$this->display('content/add');
		}else if($method=='edit'){
			if($id=$this->get('id')){
				$info = Ly_Content::getInfo($id);
				if(!$info){
					$this->error('传入ID无效！');
				}else{
					$this->assign('cinfo',$info);
				}
				if($data['title']=$this->post('title')){
					$data['tid'] = $this->post('tid');
					$data['address'] = $this->post('address');
					$data['lng'] = floatval($this->post('lng'));
					$data['lat'] = floatval($this->post('lat'));
					$data['dates'] = $this->post('dates');
					$data['content'] = $this->post('content',false);
					$data['uploads'] = join(',',$_POST['uploads']);
					$data['img'] = $this->post('img');
					$data['ishot'] = $this->post('ishot');
					if(Ly_Content::update($id,$data)){
						$this->success('修改文章成功！');
					}else{
						$this->error('修改文章失败！');
					}
				}
				$this->assign('tags',Ly_Tags::getList(0));
				$this->display('content/edit');
			}else{
				$this->error('请传入ID值！');
			}
		}else if($method=='view'){
			//查看详情
			$wid = $this->get('id');
			if($wid){
				$model = Y_Db::init('content');
				$info = $model->field(array('id','pubtime','uid','content','title','img','ishot','pubip','uploads','comments','views','shares','uid'))->find($wid);
				if($info){
					$this->assign('info',$info);
					$this->display('content/view');
				}else{
					$this->error('文章ID传入有误！');
				}
			}else{
				$this->error('请传入文章ID');
			}
			die;
		
		}else if($method=='pass'){
			if($id = $this->get('id')){
				$data['status'] = intval($this->get('v'));
				$model = Y_Db::init('content');
				$model->where($id);
				if($model->update($data)){
					$this->success('更新文章状态成功！');
				}else{
					$this->error('更新文章状态失败！');
				}
			}else{
				$this->error('请传入ID值！');
			}	
		}else if($method=='hot'){
			if($hid=$this->get('id')){
				$data['ishot'] = intval($this->get('hot'));
				if(Ly_Content::update($hid,$data)){
					$this->success('操作成功！');
				}else{
					$this->error('操作失败！');
				}
			}
		}else{
			$where = 'true';	
			//name
			if($title=$this->get('s_title')){
				$where .= " AND title like '%{$title}%' ";
			}
			//status
			if(isset($_GET['s_status'])){
				$status = join($_GET['s_status'],',');
				$where .= " AND status in({$status})";
			}
			//ishot
			if($hot=$this->get('s_ishot')){
				$where .= " AND ishot='{$hot}'";
			}
			//puttime
			if($t1=$this->get('s_pubtime1')){
				$t1 = strtotime($t1);
				$where .= " AND pubtime>='{$t1}'";
			}
			if($t2=$this->get('s_pubtime2')){
				$t2 = strtotime($t2);
				$where .= " AND pubtime<='{$t2}'";
			}
			//uid
			if($uid=$this->get('s_uid')){
				if(!is_numeric($uid)){
					$uid = Ly_User::getUid($uid);
				}
				if($uid){
					$where .= " AND uid='{$uid}'";
				}
			}
			//ID
			if($id=$this->get('s_id')){
				$where = "id='{$id}'";
			}
			$page = intval($this->get('page'));
			$page = $page?$page:1;
			$model = Y_Db::init('content');
			$model->where($where);
			$count = $model->count();
			$model->field(array('id','uid','ishot','title','img','address','dates','pubtime','status','views','comments','tid','shares'));
			$model->order('status ASC,id DESC,ishot DESC');
			$model->limit(($page-1)*10,10);
			$model->where($where);
			$this->assign('clist',$model->select());
			$page = new Y_Page($count,10);
			$this->assign('pages',$page->show());
			$this->display('content');
		}
	}
	//ajax上传图片
	public function upimgAction(){
		$method = $this->get('method');
		if($method=='edit'){
			$id = $this->post('id');
			$data['des'] = $this->post('des');
			Ly_Uploads::update($id,$data);
			die;
		}else if($method=='del'){
			$id = $this->post('id');
			$data['status'] = 3;
			Ly_Uploads::update($id,$data);
			die;
		}else{
			$info = Ly_Uploads::insert('img',1);
			if($info){
				$img['ismall'] = Y_Pr::image($info['path'],85,85);
				$img['path'] = $info['path'];
				$img['id'] = $info['id'];
				echo json_encode($img);
			}
		die;
		}	
	}
	//评论统一管理
	public function commentAction(){
		//
		$type = array('content','wayline','hotel');
		$method = $this->get('method');
		if($method=='del'){
			if($t=$this->get('t') and in_array($t,$type)){
				if($id=$this->get('id')){
					$model = Y_Db::init($t.'_comments');
					if($model->where($id)->delete()){
						$this->success('删除成功！');
					}
				}
			}
			$this->error('删除失败！');
		}else if($method=='edit'){
			if($t=$this->get('t') and in_array($t,$type)){
				if($id=$this->get('id')){
					$model = Y_Db::init($t.'_comments');
					$data[$this->post('name')] = $this->post('value');
					if($model->where($id)->update($data)){
						echo 0;
						die;
					}
					
				}
			}
			echo 1;
			die;
		}
		
		if($t=$this->get('t')){
			if(in_array($t,$type)){
				//
				$slist = Ly_Items::getList('comments','status');
				$model = Y_Db::init($t.'_comments');
				$where = "true";
				if($tid=$this->get('tid')){
					$s = substr($t,0,1).'id';
					$where .= " AND $s = {$tid}";
				}
				if($uid=$this->get('uid')){
					$where .= " AND uid={$uid}";
				}
				if($s=$this->get('status') and $s!=''){
					$s = $_GET['status'];
					$where .= " AND status in($s)";
				}
				if($id=$this->get('s_id') and is_numeric($id)){
					$where = "id={$id}";
				}
				$num = $model->where($where)->count();
				$page = $this->get('page')?intval($this->get('page')):1;
				$str = $model->where($where)->order('id desc')->limit(($page-1)*10,10)->select();
				$pages = new Y_Page($num,10);
				$pages = $pages->show();
				$this->assign('pages',$pages);
				if($str){
					$tr = '';
					if($t=='content'){
						$head = "<tr><th>ID号</th>";
						$head .= "<th>发布人</th>";
						$head .= "<th>文章</th>";
						$head .= "<th class='w250'>评论内容</th>";
						$head .= "<th>评论时间</th>";
						$head .= "<th>父级ID</th>";
						$head .= "<th>评论给</th>";
						$head .= "<th>状态</th>";
						$head .= "<th>操作</th>";
						$head .= "</tr>";
						foreach($str as $v){
							$tr .= '<tr>';
							$tr .= '<td><input type="checkbox" name="checkid" value="'.$v['id'].'" />'.$v['id'].'</td>';
							$tr .= '<td><a target="_blank" href="?ro=member&ac=member&s_uid='.$v['uid'].'">'.Ly_User::getName($v['uid']).'</a></td>';
							$tr .= '<td><a target="_blank" href="?ro=mod&ac=content&s_id='.$v['cid'].'">'.$v['cid'].'</a></td>';
							$tr .= '<td><textarea onBlur="send(this)" id='.$v['id'].' name="comments">'.$v['comments'].'</textarea></td>';
							$tr .= '<td>'.date('Y-m-d H:i',$v['pubtime']).'</td>';
							$tr .= '<td>'.$v['pid'].'</td>';
							$tr .= '<td><a target="_blank" href="?ro=member&ac=member&s_uid='.$v['touid'].'">'.$v['touid'].'</a></td>';
							$tr .= '<td>'.$slist[$v['status']]['name'].'</td>';
							$tr .= '<td><a href="?ro=mod&ac=comment&t=content&id='.$v['id'].'&method=del" class="no"><span class="icon icon-del"></span>删除</a></td>';
							$tr .= '</tr>';
						}	
					}else if($t=='wayline'){
						$head = "<tr><th>ID号</th>";
						$head .= "<th>发表者</th>";
						$head .= "<th>线路</th>";
						$head .= "<th class='w250'>评论内容</th>";
						$head .= "<th>评论时间</th>";
						$head .= "<th>好玩</th>";
						$head .= "<th>安排</th>";
						$head .= "<th>花费</th>";
						$head .= "<th>状态</th>";
						$head .= "<th>操作</th>";
						$head .= "</tr>";
						foreach($str as $v){
							$tr .= '<tr>';
							$tr .= '<td><input type="checkbox" name="checkid" value="'.$v['id'].'" />'.$v['id'].'</td>';
							$tr .= '<td><a target="_blank" href="?ro=member&ac=member&s_uid='.$v['uid'].'">'.Ly_User::getName($v['uid']).'</a></td>';
							$tr .= '<td><a target="_blank" href="?ro=mod&ac=wayline&s_wid='.$v['wid'].'">'.$v['wid'].'</a></td>';
							$tr .= '<td><textarea onBlur="send(this)" id="'.$v['id'].'" name="comments">'.$v['comments'].'</textarea></td>';
							$tr .= '<td>'.date('Y-m-d H:i',$v['pubtime']).'</td>';
							$tr .= '<td><input type="text" size=1 name="happy" id="'.$v['id'].'" onBlur="send(this)" value="'.$v['happy'].' "/></td>';
							$tr .= '<td><input type="text" size=1 name="plan" id="'.$v['id'].'" onBlur="send(this)" value="'.$v['plan'].'"/></td>';
							$tr .= '<td><input type="text" size=1 name="cost" id="'.$v['id'].'" onBlur="send(this)" value="'.$v['cost'].'"/></td>';
							$tr .= '<td>'.$slist[$v['status']]['name'].'</td>';
							$tr .= '<td><a href="?ro=mod&ac=comment&t=wayline&id='.$v['id'].'&method=del" class="no"><span class="icon icon-del"></span>删除</a></td>';
							$tr .= '</tr>';
						}
					}else if($t=='hotel'){
						$head = "<tr><th>ID号</th>";
						$head .= "<th>发表者</th>";
						$head .= "<th>酒店</th>";
						$head .= "<th class='w250'>评论内容</th>";
						$head .= "<th>评论时间</th>";
						$head .= "<th>整体</th>";
						$head .= "<th>卫生</th>";
						$head .= "<th>设备</th>";
						$head .= "<th>服务</th>";
						$head .= "<th>状态</th>";
						$head .= "<th>操作</th>";
						$head .= "</tr>";
						foreach($str as $v){
							$tr .= '<tr>';
							$tr .= '<td><input type="checkbox" name="checkid" value="'.$v['id'].'" />'.$v['id'].'</td>';
							$tr .= '<td><a href="?ro=member&ac=member&s_uid'.$v['uid'].'" target="_blank">'.Ly_User::getName($v['uid']).'</td>';
							$tr .= '<td><a href="?ro=mod&ac=hotel&s_hid='.$v['hid'].'" target="_blank">'.$v['hid'].'</a></td>';
							$tr .= '<td><textarea onBlur="send(this)" id="'.$v['id'].'" name="comments">'.$v['comments'].'</textarea></td>';
							$tr .= '<td>'.date('Y-m-d H:i',$v['pubtime']).'</td>';
							$tr .= '<td><input type="text" onBlur="send(this)" id="'.$v['id'].'" name="whole"  size=1 value="'.$v['whole'].' " /></td>';
							$tr .= '<td><input type="text" onBlur="send(this)" id="'.$v['id'].'" name="health" size=1 value="'.$v['health'].'" /></td>';
							$tr .= '<td><input type="text" onBlur="send(this)" id="'.$v['id'].'" name="device"  size=1 value="'.$v['device'].'"</td>';
							$tr .= '<td><input type="text" onBlur="send(this)" id="'.$v['id'].'" name="service"  size=1 value="'.$v['service'].'" /></td>';
							$tr .= '<td>'.$slist[$v['status']]['name'].'</td>';
							$tr .= '<td><a href="?ro=mod&ac=comment&t=hotel&id='.$v['id'].'&method=del" class="no"><span class="icon icon-del"></span>删除</a></td>';
							$tr .= '</tr>';
						}
					}
					$this->assign('strlist',$head.$tr);
				}
			}

		}
		$this->display();
	}
}
?>
