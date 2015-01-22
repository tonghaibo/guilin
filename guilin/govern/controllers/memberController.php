<?php
/**
 * 用户管理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-13
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class memberController extends Y_Controller{
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
	public function groupAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['rid']=$this->post('rid')){
				$data['name'] = $this->post('name');
				$data['rank'] = $this->post('rank');
				if(Ly_User_Rank::insert($data)){
					$this->success('添加等级组成功！');
				}else{
					$this->error('添加等级组失败！');
				}
			}
			$this->assign('max',Ly_User_Rank::getMax());
			$this->display('group/add');
		}else if($method=='edit'){
			if($rid=$this->get('rid')){
				$data['rank'] = $this->get('rank');
				$data['name'] = $this->get('name');
				$data['share'] = $this->get('share');
				$data['follow'] = $this->get('follow');
				$data['attention'] = $this->get('attention');
				$data['collect'] = $this->get('collect');
				$data['message'] = $this->get('message');
				$data['visits'] = $this->get('visits');
				$min = Ly_User_Rank::getRank($rid-1);
				if($min && $min>=$data['rank']){
					$this->error('用户等级分数限制不能低于上一级分数');
				}
				$max = Ly_User_Rank::getRank($rid+1);
				if($max && $max<$data['rank']){
					$this->error('用户等级分数限制不能大于下一级分数');

				}
				if(Ly_User_Rank::update($rid,$data)){
					$this->success('更新成功！');
				}else{
					$this->error('更新失败');

				}
			}else{
				$this->error('传入ID有误');
			}

		}else{
			$this->assign('rlist',Ly_User_Rank::getList());
			$this->display('group');
		}
	}
	//会员管理
	public function memberAction(){
		$method=$this->get('method');
		if($method=='login'){
			Y_Session::set('uid',$this->get('uid'));
			Y_Session::set('grade',$this->get('grade'));
			Y_Session::set('name',$this->get('name'));
			$this->Go('index.html');
			die;
		}else if($method=='view'){
			$uid = $this->get('uid');
			$model = Y_Db::init('user');
			$info = $model->find($uid);
			if($info){
				$this->assign('u',$info);
				$this->display('member/view');
			}else{
				$this->error('该用户不存在！');
			}
			die;
		}else if($method=='pass'){
			if($uid=$_GET['uid']){
				$data['status'] = intval($this->get('v'));
				$model = Y_Db::init('user');
				$model->where($uid);
				if($model->update($data)){
					$this->success('操作成功！');
				}else{
					$this->error('操作失败！');
				}
			}else{
				$this->error('请传入ID值！');
			}
		}else if($method=='grade'){
			if($uid=$this->get('uid')){
				$data['grade'] = intval($this->get('v'));
				$model = Y_Db::init('user');
				$model->where($uid);
				if($model->update($data)){
					$this->success('操作成功！');
				}else{
					$this->error('操作失败！');
				}
			}
		}else{	
			if($this->isAjax()){
				$upid = $this->post('upid');
				$list = Ly_Area::getList($upid);
				echo json_encode($list);
				die;
			}
			$where = 'true';
			if(isset($_GET['s_sex'])){
				$sex = $_GET['s_sex'];
				$sex = join(',',$sex);
				$where .= " and sex in($sex)";
			}
			if(isset($_GET['s_userimg'])){
				$img = $_GET['s_userimg'];
				if($img==0){
					$where .= " AND userimg=''";
				}else if($img==1){
					$where .= " AND userimg!=''";
				}
			}
			if($email=$this->get('s_email')){
				$where .= " AND email like '%$email%'";
			}
			if($name=$this->get('s_name')){
				$where .= " AND name like '%$name%'";
			}
			if(isset($_GET['s_status'])){
				$status = $_GET['s_status'];
				$status = join(',',$status);
				$where .= " and status in($status)";
			}
			if($mindate=$this->get('s_mindate')){
				$mindate = strtotime($mindate);
				$where .= " AND regtime>={$mindate}";
			}
			if($maxdate=$this->get('s_maxdate')){
				$maxdate = strtotime($maxdate);
				$where .= " AND regtime<={$maxdate}";
			}
			if($minldate=$this->get('s_minldate')){
				$minldate = strtotime($minldate);
				$where .= " AND logintime>={$minldate}";
			}
			if($maxldate=$this->get('s_maxldate')){
				$maxldate = strtotime($maxldate);
				$where .= " AND logintime<={$maxldate}";
			}
			if($city=$this->get('s_cityid')){
				$where .= " AND cityid={$city}";
			}
			if($ranklevel=$this->get('s_ranklevel')){
				$where .= " AND ranklevel={$ranklevel}";
			}
			if($grade=$this->get('s_grade') or is_numeric($grade)){
				$where .= " AND grade={$grade}";
			}
			if($uid=$this->get('s_uid')){
				$where = "uid={$uid}";
			}
			$model = Y_Db::init('user');
			$page = $_GET['page']?intval($this->get('page')):1;
			$model->where($where);
			$count = $model->count();
			$result = $model->field(array('uid','name','email','sex','userimg','rzemail','status','ranklevel','rank','grade','regtime'));
			$result = $model->order('uid DESC')->limit(($page-1)*10,10)->where($where)->select();
			$this->assign('ulist',$result);
			unset($result);
			$page = new Y_Page($count,10);
			$this->assign('pages',$page->show());
			$this->display('member');
		}
	}
	//地区
	public function areaAction(){
		$method=$this->get('method');
		if($method=='add'){
			if($this->isAjax()){
				$upid = $this->post('upid');
				$info = Ly_Area::getList($upid);
				echo json_encode($info);
				die;
			}else{
				if($data['name']=$this->post('name')){
					$data['sort'] = $this->post('sort');
					$data['upid'] = $this->post('upid');
					$data['level'] = $this->post('level');
					if(Ly_Area::insert($data)){
						$this->success('添加地区成功！');
					}else{
						$this->error('添加地区失败！');
					}
				}else{
					$alist = Ly_Area::getList();
					$this->assign('alist',$alist);
					$this->display('area/add');
				}
			}
		}else if($method=='edit'){
			if($this->isAjax()){
				$data['name'] = $this->post('name');
				$data['sort'] = $this->post('sort');
				$id = $this->post('id');
				if(Ly_Area::update($id,$data)){
					echo 0;
				}else{
					echo 1;
				}	
			}
			die;
		}else{
		if($this->isAjax()){
			$upid = $this->post('upid');
			$list = Ly_Area::getList($upid);
			echo json_encode($list);
			die;
		}
		$alist = Ly_Area::getList();
		$this->assign('alist',$alist);
		$this->display('area');
		}
	}
	//用户分组
	public function rankAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['name']=$this->post('name')){
				$data['rank']=$this->post('rank');
				$data['dayrank']=$this->post('dayrank');
				$data['type']=$this->post('type');
				if(Ly_User_Rankpower::insert($data)){
					$this->success('增加用户积分说明成功！');
				}else{
					$this->error('增加用户积分说明失败！');
				}
			}
			$this->display('rank/add');
		}else if($method=='edit'){
			$id=$this->get('id');
			$info = Ly_User_Rankpower::getInfo($id);
			if($info){
				if($data['name']=$this->post('name')){
				$data['rank']=$this->post('rank');
				$data['dayrank']=$this->post('dayrank');
				$data['type']=$this->post('type');
				if(Ly_User_Rankpower::update($id,$data)){
					$this->success('修改用户积分说明成功！');
				}else{
					$this->error('修改用户积分说明失败！');
				}
				}
				$this->assign('info',$info);
				$this->display('rank/edit');
			}else{
				$this->error('请传入ID值！');
			}
			
		}else if($method=="del"){
			$id = $this->get('id');
			$model = Y_Db::init('user_ranklog');
			if($model->where($id)->delete()){
				$this->success('删除记录成功！');
			}else{
				$this->error('删除记录失败！');
			}
		}else if($method=="award"){
			$uid = $this->get('uid');
			if($this->get('rank')==0){
				$this->error('输入奖励积分有误！');
			}
			if($rank=Ly_User::getRank($uid)){
				$data['uid'] = $uid;
				$data['rank'] = $this->get('rank')+0;
				if($data['rank']<0){
					$data['action'] = '系统处罚';
				}else{
					$data['action'] = '系统奖励';
				}
				$data['dotime'] = time();
				$data['pid'] = 0;
				$data['afterrank'] = $rank+$data['rank'];
				$data['gid'] = Y_Session::get('G_id');
				Ly_User_Ranklog::insert($data);
				$this->success('奖励积分成功！');
			}else{
				$this->error('输入用户ID不存在！');
			}
		
		}else{
			$model = Y_Db::init('user_ranklog');
			$uid = $this->get('uid');
			if($uid){
				$where = array('uid'=>$uid);
			}else{
				$where = 'true';
			}
			$num = $model->where($where)->count();
			$page = $this->get('page')?intval($this->get('page')):1;
			$llist = $model->where($where)->limit(($page-1)*10,10)->order('id DESC')->select();
			$rlist = Ly_User_Rankpower::getList();
			$pages = new Y_Page($num,10);
			$this->assign('llist',$llist);
			$this->assign('pages',$pages->show());
			$this->assign('rlist',$rlist);
			$this->display();
		}
	}
	//用户意见反馈
	public function feedbackAction(){
		$method = $this->get('method');
		if($method=="del"){
			$id = $this->get('id');
			$model = Y_Db::init('feedback');
			if($model->where($id)->delete()){
				$this->success('删除记录成功！');
			}else{
				$this->error('删除记录失败！');
			}
		}else if($method=="do"){
			$id = $this->get('id');
			$data['status'] = intval($this->get('v'));
			$model = Y_Db::init('feedback');
			if($id){
				$model->where($id)->update($data);
				$this->success('更新成功！');
			}else{
				$this->error('更新失败！');
			}
		
		}else if($method=='more'){
			$id = $this->get('id');
			$model = Y_Db::init('feedback');
			$model->where(array('id'=>$id));
			$model->field(array('contents'));
			echo '<div style="padding:5px;color:#329ECC;font-size:13px;line-height:18px;">';
			if($c=$model->find()){
				echo $c['contents'];
			}else{
				echo '参数有误！';
			}
			echo '</div>';
			die;
		}else{
			$model = Y_Db::init('feedback');
			$uid = $this->get('uid');
			$v = $this->get('v');
			$where = '';
			if($uid && $uid!=''){
				$where['uid'] = $uid;
			}
			if($v && $v!='all'){
				$where['status'] = $v;
			}
			$num = $model->where($where)->count();
			$page = $this->get('page')?intval($this->get('page')):1;
			$llist = $model->field(array('id','url','uid','pubtime','ip','browser','os','status'))->where($where)->limit(($page-1)*10,10)->order('id DESC')->select();
			$rlist = Ly_User_Rankpower::getList();
			$pages = new Y_Page($num,10);
			$this->assign('llist',$llist);
			$this->assign('pages',$pages->show());
			$this->display();
		}
	}
	//私信处理
	public function messAction(){
		$method = $this->get('method');
		if($method=="del"){
			$id = $this->get('id');
			$model = Y_Db::init('message');
			if($model->where($id)->delete()){
				$this->success('删除记录成功！');
			}else{
				$this->error('删除记录失败！');
			}
		}else if($method=='add'){
			if($this->isAjax()){
				$uid = $this->post('uid');
				$data['messages'] = $this->post('mess');
				if(strpos($uid,',')){
					//多人发送
					$uid = explode(',',$uid);
					foreach($uid as $v){
						$data['touid'] = $v;
						Ly_Message::insert($data);
					}
					die;
				}else{
					$data['touid'] = $uid;
				}
				$data['messages'] = $this->post('mess');
				if(Ly_Message::insert($data)){
					echo 0;
				}else{
					echo 1;
				}
			}
			die;
		}else if($method=="do"){
			$id = $this->get('id');
			$data['status'] = $this->get('v');
			$model = Y_Db::init('message');
			if($id){
				$model->where($id)->update($data);
				$this->success('更新成功！');
			}else{
				$this->error('更新失败！');
			}
		
		}else if($method=='getlist'){
			//获取常用语
			$list = Ly_Items::getList('messages','mess');
			if($list){
				foreach($list as $v){
					echo '<option>'.$v['name'].'</option>';
				}
			}
			die;
		}else if($method=='more'){
			$id = $this->get('id');
			$model = Y_Db::init('message');
			$model->where(array('id'=>$id));
			$model->field(array('messages'));
			echo '<div style="padding:5px;color:#329ECC;font-size:13px;line-height:18px;">';
			if($c=$model->find()){
				echo $c['messages'];
			}else{
				echo '参数有误！';
			}
			echo '</div>';
			die;
		}else{
			$model = Y_Db::init('message');
			$fromuid = $this->get('fromuid');
			$touid = $this->get('touid');
			$v = $this->get('v');
			$id = $this->get('id');
			$where = '';
			if($fromuid && $fromuid!=''){
				$where['fromuid'] = $fromuid;
			}
			if($touid && $touid!=''){
				$where['touid'] = $touid;
			}
			if($v && $v!='all'){
				$where['status'] = $v;
			}
			if($id && $id!=''){
				$where = array();
				$where['id'] = $id;
			}
			$num = $model->where($where)->count();
			$page = $this->get('page')?intval($this->get('page')):1;
			$llist = $model->field(array('id','fromuid','touid','messtime','gettime','fromdel','todel','isread','status'))->where($where)->limit(($page-1)*10,10)->order('id DESC')->select();
			$rlist = Ly_User_Rankpower::getList();
			$pages = new Y_Page($num,10);
			$this->assign('llist',$llist);
			$this->assign('pages',$pages->show());
			$this->display();
		}
	}
	//用户关系分析
	public function friendAction(){
		$uid = $this->get('uid');
		if($uid){
			$user = Ly_User::getBase($uid);
			$this->assign('user',$user);
			if($user){
				//我的信息设置
				$this->assign('messset',Ly_User_Messset::getInfo($uid));
				//我的好友
				$this->assign('myfriend',Ly_Friend::getList($uid));
				//我的粉丝
				$this->assign('myfensi',Ly_Friend::getList($uid,false));
				//我访问的人
				$this->assign('visit',Ly_User_Visit::getList($uid));
				//访问我的人
				$this->assign('visitme',Ly_User_Visit::getList($uid,false));
				//最近发布的私信
				$this->assign('messto',Ly_Message::getMylistuid($uid));
				//最近收到的私信
				$this->assign('tomess',Ly_Message::getMylistuid($uid,false));
			}
		}
		$this->display();
	}
	//认证申请
	public function applyAction(){
		$method = $this->get('method');
		if($method=='pass'){
			$id = $this->get('id');
			$data['status'] = intval($this->get('v'));
			$model = Y_Db::init('user_apply');
			if($id){
				$model->where($id)->update($data);
				if($data['status']==1){
					//更新自己的资料?
				}
				$this->success('更新成功！');
			}else{
				$this->error('更新失败！');
			}
		}else if($method=='more'){
			$id = $this->get('id');
			echo '<div style="padding:5px;font-size:13px;color:#444;line-height:18px;">';
			if($id){
				$model = Y_Db::init('user_apply');
				$model->where(array('id'=>$id));
				$model->field(array('cpname','cptel','cpaddress'));
				if($f=$model->find($id)){
					echo '<p><b>公司：</b>'.$f['cpname'].'</p>';
					echo '<p><b>公司电话：</b>'.$f['cptel'].'</p>';
					echo '<p><b>公司地址：</b>'.$f['cpaddress'].'</p>';
				}
			}else{
				echo '参数错误';
			}
			echo '</div>';
			die;
		}
		$model = Y_Db::init('user_apply');
		$where = 'true';
		if($uid=$this->get('s_uid')){
			$where .= " AND uid={$uid}";
		}
		if($v=$this->get('v') and $v!=''){
			$where .= " AND status={$v}";
		}
		if($g=$this->get('gid') and $g!=''){
			$where .= " AND gid={$g}";
		}
		if($name=$this->get('name')){
			$where .= " AND name like '%$name%'";
		}
		if($cpname=$this->get('cpname')){
			$where .= " AND cpname like '%$cpname%'";
		}
		if($id=$this->get('s_id')){
			$where = "id={$id}";
		}
		$page = $this->get('page')?intval($this->get('page')):1;
		$num = $model->field(array('id','uid','gid','card','img','applytime','name','mobile','status'))->where($where)->count();
		$this->assign('list',$model->where($where)->order('id DESC')->limit(($page-1)*10,10)->select());
		$pages = new Y_Page($num,10);
		$this->assign('pages',$pages->show());
		$this->display();
	}
}
?>
