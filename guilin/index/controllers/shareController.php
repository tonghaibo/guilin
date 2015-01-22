<?php
/**
 * 线路
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-05
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class shareController extends Y_Controller{
	public function indexAction(){
		$web = Y_Db::init('seos');
		$str = Y::$controller.'_'.Y::$action;
		$web->where(array('name'=>$str));
		$web->field(array('title','author','keywords','des'));
		$this->assign('web',$web->find());
		$model = Y_Db::init('content');
		$where1 = "img!='' AND status=1";
		$where = "z.img!='' AND z.status=1";
		$data = array();
		if($tag=$this->get('tagid')){
			if(is_numeric($tag)){
			//获取所有id
			$tag = intval($tag);
			$ids = Ly_Tags_Bind::getIds($tag,'c');
			$d='';
			if($ids){
				foreach($ids as $v){
					$d[] = $v['cid'];
				}
				$d = join(',',$d);
				$where1 .= " AND id in($d)";
				$where .= " AND z.id in($d)";
			}
			}		
		}
		if($s=$this->get('sText')){
			$where1 .= " AND title like '%{$s}%'";
			$where .= " AND z.title like '%{$s}%'";
		}
		$model->where($where1);
		$num = $model->count();
		$model->field(array('id','title','img','comments','views','pubtime'));
		$page = $this->get('page')?intval($this->get('page')):1;
		$model->where($where)->order('ishot desc,id DESC');
		$model->limit(($page-1)*20,20);
		$user = Y_Db::init('user');
		$user->where("l1.uid=z.uid");
		$user->field(array('uid','name','userimg'));
		$data = $model->join($user);
		$this->assign('list',$data);
		$pages = new Y_Page($num,20);
		$this->assign('_pages',$pages->show());
		$this->display();
	}
	public function detailAction(){
		$web = array('title'=>'文章未找到！','keywords'=>'','des'=>'');
		$info = '';
		if($id=$this->get('id') and is_numeric($id)){
			$model = Y_Db::init('content');
			$model->field(array('id','title','img','tid','address','lng','lat','content','uid','pubtime','views','comments','uploads','ishot','status','shares'));
			$model->where(array('id'=>$id,'status<'=>1));
			$info = $model->find();
			$web = array('title'=>$info['title'],'keywords'=>$info['title'],'des'=>Y_Pr::badStr(Y_Pr::substr($info['content'],0,400,false)));
			if($info['uid']!=Y_Session::get('uid') && $info['status']==0)
				$info = '';
			//cookie计数
			$co = Y_Cookie::get('c_id');
			$d['title'] = $info['title'];
			$d['time'] = time();
			$d['img'] = $info['img'];
			$d['id'] = $info['id'];
			if(empty($co)){
				//2小时
				$co['c'.$id] = $d;
				Y_cookie::set('c_id',$co,2);
				Ly_Content::update($id,array('views'=>$info['views']+1));
			}else if(!in_array('c'.$id,$co)){
				$co['c'.$id] = $d;
				$i = count($co);
				for($j=0;$j<$i-10;$j++){
					array_shift($co);
				}
				Y_cookie::set('c_id',$co,2);
				Ly_Content::update($id,array('views'=>$info['views']+1));
			}
		}
		$this->assign('web',$web);
		$this->assign('s_info',$info);
		$this->display();
	}
	public function pubAction(){
		$web = Y_Db::init('seos');
		$str = Y::$controller.'_'.Y::$action;
		$web->where(array('name'=>$str));
		$web->field(array('title','author','keywords','des'));
		$this->assign('web',$web->find());
		$this->display();
	}
	public function upimgAction(){
		$uid = Y_Session::get('uid');
		if($uid){
			$info = Y_Upload::upload('fileToUpload');
			if($info['error']==0){
				$d['path'] = $info['info'];
				$d['suffix'] = $info['sub'];
				$d['md5file'] = md5_file($info['info']);
				$d['size'] = $info['size'];
				$d['uptime'] = time();
				$d['uid'] = $uid;
				$d['issys'] = 0;
				$d['des'] = Y_Pr::substr($this->post('des'),0,30,false);
				$model = Y_Db::init('uploads');
				if($id=$model->insert($d)){
					$data['error'] = '';
					$small = Y_Pr::image($d['path'],80,80);
					$data['msg'] = array('id'=>$id,'path'=>$d['path'],'des'=>$d['des'],'small'=>$small);
				}else{
					$data['error'] = 1;
					$data['msg'] = '未知原因上传失败！';
				}
			}else{
				$data['error'] = $info['error'];
				$data['msg'] = $info['info'];
			}
		}else{
			$data['error'] = 1;
			$data['msg'] = '请先登录！';
		}
		echo json_encode($data);
		die;
	}
	//插入
	public function sendAction(){
		$uid = Y_Session::get('uid');
		if($uid){

			$data['title'] = Y_Pr::badStr($this->post('title'));
			if(!$data['title']){
				$this->error('请填写标题！');
			}
			
			$data['address'] = $this->post('address');
			$data['lng'] = $this->post('lng');
			$data['lat'] = $this->post('lat');
			$data['content'] = $this->post('content');
			if(!$data['content']){
				$this->error('请填写详细描叙！');
			}
			
			$data['content'] = Y_Ubb::init($data['content']);
			if($uploads=$_POST['uploads']){
				$data['uploads'] = join(',',$uploads);
				if(!$data['img']=$this->post('img')){
					$data['img'] = $uploads[0];
				}
			}
			$data['tid'] = $this->post('type');
			$data['uid'] = $uid;
			$data['pubtime'] = time();
			$data['pubip'] = Y_Client::ip();
			$model = Y_Db::init('content');
			if($id=$model->insert($data)){
				//加分
				Ly_User_Ranklog::setLog($uid,1);
				//绑定
				if($tags=explode(',',$this->post('tags'))){
					foreach($tags as $v){
						if(is_numeric($v)){
							$da['cid'] = $id;
							$da['tid'] = $v;
							Ly_Tags_Bind::insert($da);
						}
					}
				}
				//加记录
				$log=Y_Db::init('user');
				$log->where(array('uid'=>$uid))->update('shares=shares+1');
				$this->Go('share_detail.html?id='.$id);
			}else{
				$this->error('系统原因发布失败！');
			}
		}else{
			$this->Go('index_login.html');
		}
	}
	//评论
	public function commentAction(){
		if($this->isAjax()){
			$info['error'] = 0;
			$info['msg'] = '';
			$model = Y_Db::init('content_comments');
			$data['cid'] = $this->post('id');
			if(!$data['cid']){
				$info['error'] = 1;
				$info['msg'] = '评论或回复的文章不存在';
				echo json_encode($info);
				die;
			}
			$vcode = strtoupper($this->post('code'));
			//验证码验证
			if(Y_Session::get('verify')!=strtoupper($vcode)){
				$info['error'] = 1;
				$info['msg'] = '您输入的验证码有误!';
				echo json_encode($info);
				die;
			}
			$data['uid'] = $this->post('uid');
			if($data['uid']!=Y_Session::get('uid')){
				$info['error'] = 1;
				$info['msg'] = '请先登录';
				echo json_encode($info);
				die;
			}
			$data['comments'] = $this->post('text');
			if(!$data['comments']){
				$info['error'] = 1;
				$info['msg'] = '评论或回复不能为空';
				echo json_encode($info);
				die;
			}
			$data['touid'] = $this->post('tuid');
			$data['pid'] = $this->post('rid')?$this->post('rid'):0;
			$data['status'] = 0;
			$data['pubtime'] = time();
			$name = Ly_User::getName($data['touid']);
			if(!$name){
				$info['error'] = 1;
				$info['msg'] = '您评论的用户不存在！';
			}
			if(!$model->insert($data)){
				$info['error'] = 1;
				$info['msg'] = '系统错误！';
			}
			//文章评论加1
			$c = Y_Db::init('content');
			$c->where($data['cid'])->update('`comments`=`comments`+1');
			//加积分
			if($data['uid']!=$data['touid']){
				Ly_User_Ranklog::setLog($data['uid'],2);
			}
			//@用户
			if($data['touid']!=$data['uid']){
				$d['uid'] = $data['touid'];
				$u = Ly_User::getName($data['uid']);
				$d['title'] = '您收到<a href="user.html?uid='.$d['uid'].'" target="_blank">@'.$u.'</a>的一条评论';
				$d['notice'] = str_replace('回复@'.$name.'：','',$data['comments']);
				$d['url'] = 'share_detail.html?id='.$data['cid'];
				$d['status'] = 1;
				Ly_Notice::insert($d);
			}
			echo json_encode($info);
			die;
		}
	}
	public function commentListAction(){
		if($this->isAjax()){
			$id = $this->get('id');
			if($id){
				$model = Y_Db::init('content_comments');
				$model->field(array('id','uid','comments','touid','pubtime','pid'));
				$model->where(array('z.status'=>0,'z.cid'=>$id));
				$model->order('z.id desc');
				$u = Y_Db::init('user');
				$u->field(array('name','userimg'));
				$u->where('l1.uid=z.uid');
				$d = $model->join($u);
				$d = Y_Arr::tree($d);
				$page = $this->get('page')?intval($this->get('page')):1;
				$this->assign('comment',$d);
				$this->assign('page',$page);
				$this->display('commentList',false);
			}
		}
		die;
	}
	//编辑
	public function editAction(){
		if($id=$this->get('id')){
			$uid = Y_Session::get('uid');
			$model = Y_Db::init('content');
			$model->where(array('id'=>$id,'uid'=>$uid,'status<'=>1));
			$model->field(array('id','title','tid','content','img','uploads','address'));
			if($s=$model->find()){
				//修改
				if($this->post('btn')){
					$data['title'] = Y_Pr::badStr($this->post('title'));
					if(!$data['title']) $this->error('请填写标题！');
			
					$data['address'] = $this->post('address');
					$data['content'] = $this->post('content');
					if(!$data['content']) $this->error('请填写详细描叙！');
			
					$data['content'] = Y_Ubb::init($data['content']);
					if($uploads=$_POST['uploads']){
						$data['uploads'] = join(',',$uploads);
						if(!$data['img']=$this->post('img'))
							$data['img'] = $uploads[0];
					}
					$data['tid'] = $this->post('type');
					$data['uid'] = $uid;
					$data['pubip'] = Y_Client::ip();
					$data['status'] = 0;
					$data['ishot'] = 0;
					$model->where(array('id'=>$id,'uid'=>$uid,'status<'=>1))->update($data);
					$this->success('编辑成功！','share_detail.html?id='.$id,3);
				}
				$web = Y_Db::init('seos');
				$str = Y::$controller.'_'.Y::$action;
				$web->where(array('name'=>$str));
				$web->field(array('title','author','keywords','des'));
				$this->assign('web',$web->find());
				$this->assign('share',$s);
				$this->display();
			}else{
				$this->error('没有找到该文件，请确认地址是否正确！','share.html',3);
			}
		}else{
			$this->error('参数出错！','share.html',3);
		}
	}
}
?>
