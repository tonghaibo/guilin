<?php
/**
 * 个人中心
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-05
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class homeController extends Y_Controller{
	//
	public function __construct(){
		$web = array('title'=>'我的个人中心','keywords'=>'我的个人中心','des'=>'我的个人中心');
		$this->assign('web',$web);
		if(!$this->uid=Y_Session::get('uid'))
			$this->Go('index_login.html');
		$model = Y_Db::init('user');
		$model->where($this->uid);
		$model->fields(array('uid','name','sex','userimg','userimgflag','cityid','rank','ranklevel','views','shares','grade','attention','follow','collect'));
		if($user=$model->find()){
			$this->assign('user',$user);
			$this->cityid = $user['cityid'];
			$this->grade = $user['grade'];
			$this->provinceid = $user['provinceid'];
			unset($user);
			unset($model);
		}else{
			$this->Go('该用户不存在！');
		}
	}
	public function indexAction(){
		$this->display(null,false);
	}
	//操作
	public function doindexAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		if($do=='recom'){
			$model = Y_Db::init('content');
			$model->where(array('ishot'=>1,'status'=>1,'uid!'=>$this->uid));
			$num = $model->count();
			$page = $this->get('page')?intval($this->get('page')):1;
			$model->field(array('id','title','content','uid','pubtime','comments','shares'));
			$model->where(array('ishot'=>1,'status'=>1,'uid!'=>$this->uid));
			$model->limit(($page-1)*10,10);
			$model->order('id desc');
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('index/attention');
		}else if($do=='new'){
			$model = Y_Db::init('content');
			$model->where(array('status'=>1,'uid!'=>$this->uid));
			$num = $model->count();
			$page = $this->get('page')?intval($this->get('page')):1;
			$model->field(array('id','title','content','uid','pubtime','comments','shares'));
			$model->where(array('status'=>1,'uid!'=>$this->uid));
			$model->limit(($page-1)*10,10);
			$model->order('id desc');
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('index/attention');
		}else if($do=='follow'){
			$model = Y_Db::init('friend');
			$model->where(array('fuid'=>$this->uid));
			$model->field('uid');
			if($uid=$model->select()){
				$data = array();
				foreach($uid as $v){
					$data[] = $v['uid'];
				}
				$data = join(',',$data);
				$model = Y_Db::init('content');
				$model->where("uid in($data) and status=1");
				$num = $model->count();
				$page = $this->get('page')?intval($this->get('page')):1;
				$model->field(array('id','title','content','uid','pubtime','comments','shares'));
				$model->where("uid in($data) and status=1");
				$model->limit(($page-1)*10,10);
				$model->order('id desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,10);
				$this->assign('pages',$pages->showAjax());
			}
			$this->display('index/attention');
		}else{
			$model = Y_Db::init('friend');
			$model->where(array('uid'=>$this->uid));
			$model->field('fuid');
			if($uid=$model->select()){
				$data = array();
				foreach($uid as $v){
					$data[] = $v['fuid'];
				}
				$data = join(',',$data);
				$model = Y_Db::init('content');
				$model->where("uid in($data) and status=1");
				$num = $model->count();
				$page = $this->get('page')?intval($this->get('page')):1;
				$model->field(array('id','title','content','uid','pubtime','comments','shares'));
				$model->where("uid in($data) and status=1");
				$model->limit(($page-1)*10,10);
				$model->order('id desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,10);
				$this->assign('pages',$pages->showAjax());
			}
			$this->display('index/attention');
		}
		die;
	}
	public function shareAction(){
		$this->display(null,false);
	}
	public function doshareAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		if($do=='pic'){
			$model = Y_Db::init('uploads');
			$model->where(array('uid'=>$this->uid,'status<'=>1));
			$num = $model->count();
			$model->order('id desc');
			$page = $this->get('page')?intval($this->get('page')):1;
			$model->field(array('id','path','des','uptime'));
			$model->where(array('uid'=>$this->uid,'status<'=>1));
			$model->limit(($page-1)*9,9);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,9);
			$this->assign('pages',$pages->showAjax());
			$this->display('share/pic');		
		}elseif($do=='del'){
			if($id=$this->get('id')){
				$model = Y_Db::init('content');
				$model->where(array('uid'=>$this->uid,'id'=>$id));
				if($model->update(array('status'=>2))){
					Ly_User_Ranklog::setLog($this->uid,11);
					$model = Y_Db::init('user');
					$model->where(array('uid'=>$this->uid))->update("shares=shares-1");
					die('1');
				}
			}
			die('0');
		}else{
			$model = Y_Db::init('content');
			$model->where(array('uid'=>$this->uid,'status<'=>1));
			$num = $model->count();
			$model->order('id desc');
			$page = $this->get('page')?intval($this->get('page')):1;
			$model->field(array('id','title','content','uid','pubtime','comments','shares'));
			$model->where(array('uid'=>$this->uid,'status<'=>1));
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('share/content');
		}
		die;
	}
	public function collectAction(){
		$this->display(null,false);
	}
	public function docollectAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		if($do=='hotel'){
			$page = $this->get('page')?intval($this->get('page')):1;
			$model = Y_Db::init('collect');
			$model->where(array('uid'=>$this->uid,'type'=>'h'));
			$num = $model->count();
			$model->where(array('uid'=>$this->uid,'type'=>'h'));
			$model->field(array('id','collecttime'));
			$model->order('collecttime desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('collect/hotel');
		}else if($do=='wayline'){
			$page = $this->get('page')?intval($this->get('page')):1;
			$model = Y_Db::init('collect');
			$model->where(array('uid'=>$this->uid,'type'=>'w'));
			$num = $model->count();
			$model->where(array('uid'=>$this->uid,'type'=>'w'));
			$model->field(array('id','collecttime'));
			$model->order('collecttime desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('collect/wayline');
		}else if($do=='del'){
			if($id=$this->get('id')){
				$t = strtolower($this->get('t'));
				$model = Y_Db::init('collect');
				$model->where(array('uid'=>$this->uid,'id'=>$id,'type'=>$t));
				if($model->delete()){
					//加记录
					$log=Y_Db::init('user');
					$log->where(array('uid'=>$this->uid))->update('collect=collect-1');
					die('1');
				}
			}
			die('0');
		}else{
			$page = $this->get('page')?intval($this->get('page')):1;
			$model = Y_Db::init('collect');
			$model->where(array('uid'=>$this->uid,'type'=>'c'));
			$num = $model->count();
			$model->where(array('uid'=>$this->uid,'type'=>'c'));
			$model->field(array('id','collecttime'));
			$model->order('collecttime desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('collect/share');
		}
	}
	public function orderAction(){
		$this->display(null,false);
	}
	public function doorderAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		$page = $this->get('page')?intval($this->get('page')):1;
		if($do=='wayline'){
			$model = Y_Db::init('wayline_order');
			$model->where(array('uid'=>$this->uid,'status<'=>7));
			$num = $model->count();
			$model->field(array('id','wid','ordertime','handsel','isorder','status'));
			$model->where(array('uid'=>$this->uid,'status<'=>7));
			$model->limit(($page-1)*10,10);
			$model->order('id desc');
			$pages = new Y_Page($num,10);
			$this->assign('info',$model->select());
			$this->assign('pages',$pages->showAjax());
			$this->display('order/wayline',false);
		}else if($do=='hdetail'){
			if($oid = $this->post('oid')){
				$model = Y_Db::init('hotel_order');
				$model->where(array('id'=>$oid));
				$model->field(array('rid','hid','starttime','days','num','price','name','mobile','mark','card'));
				if($info=$model->find()){
					$this->assign('info',$info);
					$this->display('order/hdetail');
				}else die('该订单不存在！');
			}else{
				die('错误参数！');
			}
		}else if($do=='wdetail'){
			if($oid = $this->post('oid')){
				$model = Y_Db::init('wayline_order');
				$model->where(array('id'=>$oid));
				$model->field(array('wid','starttime','adult','child','price','name','mobile','mark','card'));
				if($info=$model->find()){
					$this->assign('info',$info);
					$this->display('order/wdetail');
				}else die('该订单不存在！');
			}else{
				die('错误参数！');
			}
		}else if($do=='cancel'){
			$oid = $this->post('oid');
			$t = $this->post('t');
			if(!$oid) die('1');
			if($t=='h'){
				$model = Y_Db::init('hotel_order');
				$model->where(array('id'=>$oid,'uid'=>$this->uid));
				$model->update(array('status'=>1));
				die('0');
			}else if($t=='w'){
				$model = Y_Db::init('wayline_order');
				$model->where(array('id'=>$oid,'uid'=>$this->uid));
				$model->update(array('status'=>1));
				die('0');
			}
			die('1');
		}else{
			$model = Y_Db::init('hotel_order');
			$model->where(array('uid'=>$this->uid,'status<'=>7));
			$num = $model->count();
			$model->field(array('id','hid','ordertime','handsel','isorder','status'));
			$model->where(array('uid'=>$this->uid,'status<'=>7));
			$model->limit(($page-1)*10,10);
			$model->order('id desc');
			$pages = new Y_Page($num,10);
			$this->assign('info',$model->select());
			$this->assign('pages',$pages->showAjax());
			$this->display('order/hotel',false);
		}
	}
	public function messAction(){
		$this->display(null,false);
	}
	public function domessAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		$page = $this->get('page')?intval($this->get('page')):1;
		if($do=='other'){
			$model = Y_Db::init('message');
			$model->where(array('fromuid'=>$this->uid,'fromdel'=>0,'status<'=>1));
			$num = $model->count();
			$model->where(array('fromuid'=>$this->uid,'fromdel'=>0,'status<'=>1));
			$model->field(array('id','touid','messages','messtime','gettime','isread'));
			$model->order('id desc');
			$model->limit(($page-1)*10,10);
			$pages = new Y_Page($num,10);
			$this->assign('info',$model->select());
			$this->assign('pages',$pages->showAjax());
			$this->display('mess/other');
		}else if($do=='isread'){
			$model = Y_Db::init('message');
			$model->where(array('touid'=>$this->uid,'todel'=>0,'status<'=>1,'isread'=>1));
			$num = $model->count();
			$model->where(array('touid'=>$this->uid,'todel'=>0,'status<'=>1,'isread'=>1));
			$model->field(array('id','fromuid','messages','messtime','gettime','isread'));
			$model->order('id desc');
			$model->limit(($page-1)*10,10);
			$pages = new Y_Page($num,10);
			$this->assign('info',$model->select());
			$this->assign('pages',$pages->showAjax());
			$this->display('mess/isread');
		}else if($do=='read'){
			if($id=$this->get('id')){
				$model = Y_Db::init('message');
				$model->where(array('id'=>$id,'touid'=>$this->uid));
				$model->update(array('isread'=>1,'gettime'=>time()));
			}
		}else if($do=='del'){
			if($id=$this->get('id')){
				$model = Y_Db::init('message');
				$t = $this->get('t');
				if($t=='t'){
					$model->where(array('id'=>$id,'touid'=>$this->uid));
					$model->update(array('todel'=>1));
				}else{
					$model->where(array('id'=>$id,'fromuid'=>$this->uid));
					$model->update(array('fromdel'=>1));
				}
			}
		}else{
			$model = Y_Db::init('message');
			$model->where(array('touid'=>$this->uid,'todel'=>0,'status<'=>1,'isread'=>0));
			$num = $model->count();
			$model->where(array('touid'=>$this->uid,'todel'=>0,'status<'=>1,'isread'=>0));
			$model->field(array('id','fromuid','messages','messtime','gettime','isread'));
			$model->order('id desc');
			$model->limit(($page-1)*10,10);
			$pages = new Y_Page($num,10);
			$this->assign('info',$model->select());
			$this->assign('pages',$pages->showAjax());
			$this->display('mess/me');	
		}
		die;
	}
	public function baseAction(){
		$this->display(null,false);
	}
	public function dobaseAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		if($do=='img'){
			$this->display('base/img');
		}else if($do=='cropimg'){
			if($img=$this->post('path')){
				$x2 = $this->post('x2');
				$x1 = $this->post('x1');
				$y2 = $this->post('y2');
				$y1 = $this->post('y1');
				if($y2-$y1<80 || $x2-$x1<80) die(3);
				if(Y_Image_Crop::crop($img,$img,$x1,$y1,$x2,$y2,185,185)){
					$model = Y_Db::init('user');
					$model->where(array('uid'=>$this->uid));
					$model->update(array('userimg'=>$img,'userimgflag'=>0));
					//第一次上传
					if(!$this->post('d_src')){
						Ly_User_Ranklog::setLog($this->uid,16);
					}
					die('0');
				
				}else{
					die(2);
				}
			}
			die(1);
		}else if($do=='area'){
			$this->display('base/area');
		}else if($do=='uptags'){
			if($val=$_POST['val']){
				$model = Y_Db::init('tags_bind');
				$model->where(array('uid'=>$this->uid));
				$model->delete();
				for($i=0;$i<5;$i++){
					$model->insert(array('tid'=>$val[$i],'wid'=>0,'hid'=>0,'cid'=>0,'uid'=>$this->uid));
				}
				die('0');
			}
			die('1');
		}else if($do=='edpass'){
			$this->display('base/edpass');
		}else if($do=='uppass'){
			if($old = $this->post('old')){
				$old = md5($old);
				$model = Y_Db::init('user');
				$model->where(array('uid'=>$this->uid));
				$model->field('password');
				$pass = $model->find();
				if($old!=$pass['password']) die('1');
				$newpwd = $this->post('newpwd');
				if(Y_Check::password($newpwd)){
					$model->where(array('uid'=>$this->uid));
					$model->update(array('password'=>md5($newpwd)));
					die('0');	
				}
			}
			die('2');
		}else if($do=='share'){
			$this->display('base/share');
		}else if($do=='up'){
			$info['error'] = 0;
			$info['msg'] = '';
			$data['name'] = $this->post('name');
			if(!Y_Check::Len($data['name'])){
				$info['error'] = 1;
				$info['msg'] = '昵称字符数不合法';
				echo json_encode($info);
				die;
			}
			$data['sex'] = intval($this->post('sex'));
			$data['birth_year'] = intval($this->post('year'));
			if(!$data['birth_year']){
				$info['error'] = 2;
				$info['msg'] = '请输入生日年份';
				echo json_encode($info);
				die;
			}
			$data['birth_month'] = intval($this->post('month'));
			if($data['birth_month']<1 || $data['birth_month']>12){
				$info['error'] = 3;
				$info['msg'] = '生日月份不合法';
				echo json_encode($info);
				die;
			}
			$data['birth_day'] = intval($this->post('day'));
			if($data['birth_day']<1 || $data['birth_day']>31){
				$info['error'] = 4;
				$info['msg'] = '生日日期不合法';
				echo json_encode($info);
				die;
			}
			$data['birthday'] = $data['birth_year'].'-'.$data['birth_month'].'-'.$data['birth_day'];
			$data['provinceid'] = intval($this->post('provinceid'));
			$data['cityid'] = intval($this->post('cityid'));
			$data['jobs'] = intval($this->post('jobs'));
			$data['education'] = intval($this->post('education'));
			$data['income'] = intval($this->post('income'));
			$data['sign'] = $this->post('sign');
			if(!Y_Check::len($data['sign'],2,140)){
				$info['error'] = 5;
				$info['msg'] = '个性签名字符数不合法';
				echo json_encode($info);
				die;
			}
			$data['signview'] = 0;
			$model = Y_Db::init('user');
			$model->where(array('uid'=>$this->uid));
			$model->update($data);
			echo json_encode($info);
			die;
		}else if($do=='getCity'){
			if($pid=$this->post('pid'));
			$list = Ly_Area::getList($pid);
			if($list){
				echo json_encode($list);
			}
			die;
		}else{
			$model = Y_Db::init('user');
			$model->field(array('name','sex','birthday','birth_year','birth_month','birth_day','provinceid','cityid','jobs','education','income','sign','signview'));
			$model->where(array('uid'=>$this->uid));
			$this->assign('info',$model->find());
			$this->display('base/index');
		}
		die;
	}
	public function upimgAction(){
		$info = Y_Upload::upload('fileToUpload');
		if($info['error']!=0){
			echo json_encode(array('error'=>$info['error'],'msg'=>$info['info']));
			die;
		}
		//判断尺寸
		$img = Y_Image::getImageInfo($info['info']);
		if($img['width']<150 || $img['height']<150){
			//删除
			@unlink($info['info']);
			echo json_encode(array('error'=>1,'msg'=>'图像尺寸在150x150之间'));
			die;
		}
		//插入
		$d['path'] = Y_Image_Thumb::thumb($info['info'],$info['info'],300,300);
		$d['suffix'] = $info['sub'];
		$d['md5file'] = md5_file($info['info']);
		$d['size'] = $info['size'];
		$d['uptime'] = time();
		$d['uid'] = $this->uid;
		$d['issys'] = 0;
		$d['des'] = $this->post('des');
		$model = Y_Db::init('uploads');
		if($id=$model->insert($d)){
			$data['error'] = '';

			$data['msg'] = array('id'=>$id,'path'=>$d['path']);
			echo json_encode($data);
			die;
		}else{
			echo json_encode(array('error'=>1,'msg'=>'未知原因,上传图片失败!'));
			die;
		}
	}
		
	public function noticeAction(){
		$this->display(null,false);
	}
	public function donoticeAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		if($do=='config'){
			$this->assign('info',Ly_User_Messset::getInfo($this->uid));
			$this->display('notice/config');
		}else if($do=='upconfig'){
			$data['comments'] = $this->post('comments')?1:0;
			$data['attention'] = $this->post('attention')?1:0;
			$data['replay'] = $this->post('replay')?1:0;
			$data['mail'] = $this->post('mail')?1:0;
			$data['mess'] = $this->post('mess')+0;
			$model = Y_Db::init('user_messset');
			$model->where(array('uid'=>$this->uid))->delete();
			$data['uid'] = $this->uid;
			$model->insert($data);
			die('1');
		}else if($do=='del'){
			if($id=$this->get('id')){
				$model = Y_Db::init('notice');
				$model->where(array('id'=>$id,'uid'=>$this->uid));
				$model->update(array('status'=>2));
			}
		}else{
			$model = Y_Db::init('notice');
			$page = $this->get('page')?intval($this->get('page')):1;
			$model->where("uid in(0,{$this->uid}) AND status=1");
			$num = $model->count();
			$model->where("uid in(0,{$this->uid}) AND status=1");
			$model->order('id desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('notice/index');
		}
		die;
	}
	public function rankAction(){
		$this->display(null,false);
	}
	public function dorankAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		if($do=='log'){
			$model = Y_Db::init('user_ranklog');
			$page = $this->get('page')?intval($this->get('page')):1;
			$model->where(array('uid'=>$this->uid));
			$num = $model->count();
			$model->where(array('uid'=>$this->uid));
			$model->field(array('id','pid','action','rank','dotime','afterrank'));
			$model->order('id desc');
			$model->limit(($page-1)*10,10);
			$pages = new Y_Page($num,10);
			$this->assign('info',$model->select());
			$this->assign('pages',$pages->showAjax());
			$this->display('rank/log');
		}else if($do=='rule'){
			$model = Y_Db::init('user_rankpower');
			$this->assign('info',$model->select());
			$this->display('rank/rule');
		
		}else{
			$model = Y_Db::init('user_rank');
			$model->order('rid asc');
			$this->assign('info',$model->select());
			$this->display('rank/index');
		}
		die;
	}
	//好友
	public function friendAction(){
		$this->display(null,false);
		die;
	}
	public function dofriendAction(){
		$this->isAjax() or die;
		$do = $this->get('do');
		if($do=='follow'){
			$model = Y_Db::init('friend');
			$model->where(array('fuid'=>$this->uid));
			$num = $model->count();
			$model->where(array('fuid'=>$this->uid));
			$model->field(array('uid','pubtime','name'));
			$pages = $this->get('page')?intval($this->get('page')):1;
			$model->limit(($pages-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('friend/follow');
			die;
		}else if($do=='invite'){
			$this->display('friend/invite');
			die;
		}else if($do=='guess'){
			$this->display('friend/guess');
			die;
		}else if($do=='changeguess'){
			$act = $this->get('act');
			$model = Y_Db::init('friend');
			$model->where(array('uid'=>$this->uid));
			$model->field("group_concat(fuid) as f ");
			$f = $model->find();
			$where = 'true';
			if($f){
				$where .= " AND uid not in({$f['f']},{$this->uid})";
			}else{
				$where .= " AND uid not in({$this->uid})";
			}
			$model = Y_Db::init('user');
			if($act=='vip'){
				$where .= ' AND rzcredit=1';
			}elseif($act=='area'){
				$where .= " AND cityid={$this->cityid}";
			}else if($act=='wantgo'){
				$area = Ly_Tags_Bind::getTags(array('uid'=>$this->uid),true);
				$uids = Ly_Tags_Bind::getIds($area,'u',true);
				if($uids){
					$where .= " AND uid in($uids)";
				}			
			}else{
				die('错误参数！');
			}
			$model->where($where);
			$model->field(array('uid','name','userimg','userimgflag','provinceid','cityid','shares','follow','sign','signview'));
			$model->order('rand()');
			$model->limit(10);
			$this->assign('info',$model->select());
			$this->display('friend/changeguess');
			die;
		}else{
			$model = Y_Db::init('friend');
			$model->where(array('uid'=>$this->uid));
			$num = $model->count();
			$model->where(array('uid'=>$this->uid));
			$model->field(array('fuid','pubtime','name'));
			$pages = $this->get('page')?intval($this->get('page')):1;
			$model->limit(($pages-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('friend/index');
			die;
		}
	}
	//我的酒店
	public function hotelAction(){
		if($this->grade!=3 && $this->grade!=1) die;
		$this->display('hotel');
	}
	public function dohotelAction(){
		$this->isAjax() or die();
		if($this->grade!=2 && $this->grade!=1) die;
		$do = $this->get('do');
		$page = $this->get('page')?intval($this->get('page')):1;
		if($do=='order'){
			$this->display('hotel/order');
		}else if($do=='list'){
			$model = Y_Db::init('hotel');
			$model->where(array('uid'=>$this->uid));
			$model->field("group_concat(hid) as f");
			$f = $model->find();
			if(!$f) die('您还没有添加任何酒店');
			$where = "hid in({$f['f']})";
			$t = $this->get('t');
			if($t=='t1'){
				$where .= " AND status in(0,2)";
			}else if($t=='t2'){
				$where .= " AND status in(3,4)";
			}else if($t=='t3'){
				$where .= " AND status in(6,7)";
			}else if($t=='t4'){
				$where .= " AND status=5";
			}else{
				$where .= " AND status in(0,2,3,4,5,6,7)";
			}
			$txt = $this->get('txt');
			if($txt){
				$s = $this->get('s');
				if($s==1){
					$where .= " AND  id like '$txt%'";
				}else if($s==2){
					$where .= " AND  hid=$txt";
				}else if($s==3){
					$txt = strtotime($txt);
					$where .= " AND ordertime>$txt";
				}else if($s==4){
					$where .= " AND card like '$txt%'";
				}else if($s==5){
					$where .= " AND mobile=$txt";
				}else if($s==6){
					$where .= " AND name like '$txt%'";
				}
			}
			$model = Y_Db::init('hotel_order');
			$num = $model->where($where)->count();
			$model->where($where);
			$model->field(array('id','hid','uid','name','mobile','card','status','ordertime','oktime'));
			$model->order('id desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('hotel/list',false);
		}else if($do=='detail'){
			if($oid = $this->post('oid')){
				$model = Y_Db::init('hotel_order');
				$model->where(array('id'=>$oid));
				$model->field(array('rid','hid','starttime','days','num','price','mark','card','isorder','handsel','oktime'));
				if($info=$model->find()){
					$this->assign('info',$info);
					$this->display('hotel/detail');
				}else die('该订单不存在！');
			}else{
				die('错误参数！');
			}
		}else if($do=='add'){
			if($this->post('send')){
				$data['classes'] = intval($this->post('classes'));
				if($data['classes']==0) die('1');
				$data['star'] = intval($this->post('star'));
				$data['name'] =	$this->post('name');
				if(!$data['name']) die('2');
				$data['area'] = intval($this->post('area'));
				if($data['area']==0) die('3');
				$data['address'] = $this->post('address');
				if(!$data['address']) die('4');
				$data['price'] = $this->post('price');
				if(!$data['price']) die('5');
				$data['traffic'] = $this->post('traffic');
				$data['contact'] = $this->post('contact');
				if(!$data['contact']) die('6');
				$data['portraiture'] = $this->post('fax');
				$data['ctime'] = $this->post('cdate');
				$data['des'] = $this->post('des');
				if(!$data['des']) die('7');
				$data['servers'] = $this->post('servers');
				$data['relaxation'] = $this->post('relaxation');
				$data['dc'] = $this->post('dc');
				$data['notice'] = $this->post('notice');
				$data['uploads'] = $this->post('uploads');
				$data['img'] = $this->post('img');
				if($data['img']) $data['img'] = Ly_Uploads::getUrl($data['img']);
				$data['uid'] = $this->uid;
				$data['pubtime'] = time();
				$model = Y_Db::init('hotel');
				if($id=$model->insert($data)){
					$rname = $_POST['roomname'];
					$rprice = $_POST['roomprice'];
					$rbed = $_POST['roombed'];
					$rbroadband = $_POST['roombroadband'];
					$rbreakfast = $_POST['roombreakfast'];
					$rpayment = $_POST['roompayment'];
					$rarea = $_POST['roomsize'];
					$room = Y_Db::init('hotel_rooms');
					$len = count($rname);
					for($i=0;$i<$len;$i++){
						$d['name'] = $this->filter($rname[$i]);
						$d['price'] = $this->filter($rprice[$i]);
						$d['bed'] = intval($rbed[$i]);
						$d['broadband'] = intval($rbroadband[$i]);
						$d['breakfast'] = intval($rbreakfast[$i]);
						$d['payment'] = intval($rpayment[$i]);
						$d['area'] = intval($rarea[$i]);
						$d['hid'] = $id;
						$room->insert($d);
					}
				}
				die('0');
			}
			$this->display('hotel/add');
		}else if($do=='edit'){
			$model = Y_Db::init('hotel');
			if($this->post('send')){
				$data['classes'] = intval($this->post('classes'));
				if($data['classes']==0) die('1');
				$data['star'] = intval($this->post('star'));
				$data['name'] =	$this->post('name');
				if(!$data['name']) die('2');
				$data['area'] = intval($this->post('area'));
				if($data['area']==0) die('3');
				$data['address'] = $this->post('address');
				if(!$data['address']) die('4');
				$data['price'] = $this->post('price');
				if(!$data['price']) die('5');
				$data['traffic'] = $this->post('traffic');
				$data['contact'] = $this->post('contact');
				if(!$data['contact']) die('6');
				$data['portraiture'] = $this->post('fax');
				$data['ctime'] = $this->post('cdate');
				$data['des'] = $this->post('des');
				if(!$data['des']) die('7');
				$data['servers'] = $this->post('servers');
				$data['relaxation'] = $this->post('relaxation');
				$data['dc'] = $this->post('dc');
				$data['notice'] = $this->post('notice');
				$data['uploads'] = $this->post('uploads');
				$data['img'] = $this->post('img');
				if($data['img']) $data['img'] = Ly_Uploads::getUrl($data['img']);
				$data['uid'] = $this->uid;
				$data['status'] = 0;
				$model->where(array('uid'=>$this->uid,'hid'=>$page));
				$model->update($data);
				$rrid = $_POST['roomrid'];
				$rname = $_POST['roomname'];
				$rprice = $_POST['roomprice'];
				$rbed = $_POST['roombed'];
				$rbroadband = $_POST['roombroadband'];
				$rbreakfast = $_POST['roombreakfast'];
				$rpayment = $_POST['roompayment'];
				$rarea = $_POST['roomsize'];
				$room = Y_Db::init('hotel_rooms');
				$len = count($rname);
				for($i=0;$i<$len;$i++){
					$d['name'] = $this->filter($rname[$i]);
					$d['price'] = $this->filter($rprice[$i]);
					$d['bed'] = intval($rbed[$i]);
					$d['broadband'] = intval($rbroadband[$i]);
					$d['breakfast'] = intval($rbreakfast[$i]);
					$d['payment'] = intval($rpayment[$i]);
					$d['area'] = intval($rarea[$i]);
					$d['hid'] = $page;
					if($rrid[$i]){
						$room->where(array('rid'=>$rrid[$i]))->update($d);
					}else{
						$room->insert($d);
					}
				}
				die('0');
			}
			$model->where(array('uid'=>$this->uid,'hid'=>$page));
			$model->field(array('hid','name','classes','star','des','img','area','address','price','traffic','ctime','contact','servers','relaxation','notice','dc','portraiture','uploads'));
			$this->assign('info',$model->find());
			$this->display('hotel/edit');
		}else if($do=='room'){
			$model = Y_Db::init('hotel_rooms');
			$rid = $this->post('rid');
			$hid = $this->post('hid');
			$model->where(array('rid'=>$rid,'hid'=>$hid))->update(array('status'=>2));
		}else if($do=='act'){
			$up['status'] = intval($this->post('t'));
			$oid = $this->post('oid');
			if(!$oid) die('1');
			$model = Y_Db::init('hotel_order');
			if($up['status']==6){
				$up['isorder'] = 1;
				$up['oktime'] = time();
				//是否发消息
			}
			$model->where(array('id'=>$oid));
			$model->update($up);
			die('0');
		}else if($do=='do'){
			$s = intval($this->post('s'));
			$hid = $this->post('hid');
			if($hid){
				$model = Y_Db::init('hotel');
				$model->where(array('hid'=>$hid,'uid'=>$this->uid));
				$model->update(array('status'=>$s));
				die('0');
			}
			die('1');
		}else{
			$model = Y_Db::init('hotel');
			$model->where(array('uid'=>$this->uid,'status<'=>2));
			$num = $model->count();
			$model->where(array('uid'=>$this->uid,'status<'=>2));
			$model->field(array('hid','name','pubtime','status','ishot'));
			$model->order('ishot desc,status asc,hid desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('hotel/hotel',false);

		}
	}
	//我的线路
	public function waylineAction(){
		if($this->grade!=3 && $this->grade!=1) die;
		$this->display('wayline',false);
	}
	public function dowaylineAction(){
		$this->isAjax() or die();
		if($this->grade!=3 && $this->grade!=1) die;
		$do = $this->get('do');
		$page = $this->get('page')?intval($this->get('page')):1;
		if($do=='order'){
			$this->display('wayline/order');
		}else if($do=='list'){
			$model = Y_Db::init('wayline');
			$model->where(array('uid'=>$this->uid));
			$model->field("group_concat(wid) as f");
			$f = $model->find();
			if(!$f) die('您还没有添加任何线路');
			$where = "wid in({$f['f']})";
			$t = $this->get('t');
			if($t=='t1'){
				$where .= " AND status in(0,2)";
			}else if($t=='t2'){
				$where .= " AND status in(3,4)";
			}else if($t=='t3'){
				$where .= " AND status in(6,7)";
			}else if($t=='t4'){
				$where .= " AND status=5";
			}else{
				$where .= " AND status in(0,2,3,4,5,6,7)";
			}
			$txt = $this->get('txt');
			if($txt){
				$s = $this->get('s');
				if($s==1){
					$where .= " AND  id like '$txt%'";
				}else if($s==2){
					$where .= " AND  wid=$txt";
				}else if($s==3){
					$txt = strtotime($txt);
					$where .= " AND ordertime>$txt";
				}else if($s==4){
					$where .= " AND card like '$txt%'";
				}else if($s==5){
					$where .= " AND mobile=$txt";
				}else if($s==6){
					$where .= " AND name like '$txt%'";
				}
			}
			$model = Y_Db::init('wayline_order');
			$num = $model->where($where)->count();
			$model->where($where);
			$model->field(array('id','wid','uid','name','mobile','card','status','ordertime','oktime'));
			$model->order('id desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('wayline/list',false);
		}else if($do=='detail'){
			if($oid = $this->post('oid')){
				$model = Y_Db::init('wayline_order');
				$model->where(array('id'=>$oid));
				$model->field(array('wid','starttime','adult','child','price','mark','card','isorder','handsel','oktime'));
				if($info=$model->find()){
					$this->assign('info',$info);
					$this->display('wayline/detail');
				}else die('该订单不存在！');
			}else{
				die('错误参数！');
			}
		}else if($do=='add'){
			if($this->post('send')){
				$data['name'] =	$this->post('name');
				if(!$data['name']) die('1');
				$data['img'] = $this->post('img');
				$data['price'] = $this->post('price');
				if(!$data['price']) die('2');
				$data['pricehalf'] = $this->post('pricehalf');
				if(!$data['pricehalf']) die('3');
				$data['traffic'] = $this->post('traffic');
				$data['contact'] = $this->post('contact');
				if(!$data['contact']) die('4');
				$data['days'] = intval($this->post('days'));
				if($data['days']<1 || $data['days']>10) die('5');
				$data['pricein'] = $this->post('pricein');
				$data['priceout'] = $this->post('priceout');
				$data['notice'] = $this->post('notice');
				$data['uid'] = $this->uid;
				$data['pubtime'] = time();
				$model = Y_Db::init('wayline');
				if($id=$model->insert($data)){
					$dtitle = $_POST['daytitle'];
					$ddes = $_POST['daydes'];
					$deat1 = $_POST['dayeat1'];
					$deat2 = $_POST['dayeat2'];
					$deat3 = $_POST['dayeat3'];
					$dlive = $_POST['daylive'];
					$days = Y_Db::init('wayline_days');
					for($i=0;$i<$data['days'];$i++){
						$d['name'] = $this->filter($dtitle[$i]);
						$d['contents'] = $this->filter($ddes[$i]);
						$d['eat1'] = intval($deat1[$i]);
						$d['eat2'] = intval($deat2[$i]);
						$d['eat3'] = intval($deat3[$i]);
						$d['live'] = $this->filter($dlive[$i]);
						$d['daynum'] = $i+1;
						$d['wid'] = $id;
						$days->insert($d);
					}
				}
				die('0');
			}
			$this->display('wayline/add');
		}else if($do=='edit'){
			$model = Y_Db::init('wayline');
			if($this->post('send')){
				$wid = $this->post('wid');
				if(!$wid) die('5');
				$data['name'] =	$this->post('name');
				if(!$data['name']) die('1');
				$data['img'] = $this->post('img');
				$data['price'] = $this->post('price');
				if(!$data['price']) die('2');
				$data['pricehalf'] = $this->post('pricehalf');
				if(!$data['pricehalf']) die('3');
				$data['traffic'] = $this->post('traffic');
				$data['contact'] = $this->post('contact');
				if(!$data['contact']) die('4');
				$data['days'] = intval($this->post('days'));
				if($data['days']<1 || $data['days']>10) die('5');
				$data['pricein'] = $this->post('pricein');
				$data['priceout'] = $this->post('priceout');
				$data['notice'] = $this->post('notice');
				$data['uid'] = $this->uid;
				$data['status'] = 0;
				$model = Y_Db::init('wayline');
				$model->where(array('wid'=>$wid))->update($data);
				$days = Y_Db::init('wayline_days');
				$days->where(array('wid'=>$wid))->delete();
				$dtitle = $_POST['daytitle'];
				$ddes = $_POST['daydes'];
				$deat1 = $_POST['dayeat1'];
				$deat2 = $_POST['dayeat2'];
				$deat3 = $_POST['dayeat3'];
				$dlive = $_POST['daylive'];
				for($i=0;$i<$data['days'];$i++){
					$d['name'] = $this->filter($dtitle[$i]);
					$d['contents'] = $this->filter($ddes[$i]);
					$d['eat1'] = intval($deat1[$i]);
					$d['eat2'] = intval($deat2[$i]);
					$d['eat3'] = intval($deat3[$i]);
					$d['live'] = $this->filter($dlive[$i]);
					$d['daynum'] = $i+1;
					$d['wid'] = $wid;
					$days->insert($d);
				}
				die('0');
			}
			$model->where(array('uid'=>$this->uid,'wid'=>$page));
			$model->field(array('wid','name','img','price','pricehalf','pricein','traffic','priceout','contact','days','notice'));
			$this->assign('info',$model->find());
			$this->display('wayline/edit');
		}else if($do=='room'){
			$model = Y_Db::init('hotel_rooms');
			$rid = $this->post('rid');
			$hid = $this->post('hid');
			$model->where(array('rid'=>$rid,'hid'=>$hid))->update(array('status'=>2));
		}else if($do=='act'){
			$up['status'] = intval($this->post('t'));
			$oid = $this->post('oid');
			if(!$oid) die('1');
			$model = Y_Db::init('wayline_order');
			if($up['status']==6){
				$up['isorder'] = 1;
				$up['oktime'] = time();
				//是否发消息
			}
			$model->where(array('id'=>$oid));
			$model->update($up);
			die('0');
		}else if($do=='do'){
			$s = intval($this->post('s'));
			$wid = $this->post('wid');
			if($wid){
				$model = Y_Db::init('wayline');
				$model->where(array('wid'=>$wid,'uid'=>$this->uid));
				$model->update(array('status'=>$s));
				die('0');
			}
			die('1');
		}else{
			$model = Y_Db::init('wayline');
			$model->where(array('uid'=>$this->uid,'status<'=>2));
			$num = $model->count();
			$model->where(array('uid'=>$this->uid,'status<'=>2));
			$model->field(array('wid','name','pubtime','status','ishot'));
			$model->order('ishot desc,status asc,wid desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->showAjax());
			$this->display('wayline/wayline',false);

		}
	}
}
?>
