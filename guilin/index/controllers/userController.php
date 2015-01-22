<?php
/**
 * 
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-24
 * @package default
 * @license http:://weibo.com/yuexinok
 **/

class userController extends Y_Controller{
	public $uid;
	public function __construct(){
		$this->uid = $this->get('uid')?$this->get('uid'):Y_Session::get('uid');
	}
	public function indexAction(){
		$web = array('title'=>'未找到该用户！','keywords'=>'','des'=>'');
		$model = Y_Db::init('user');
		$model->where(array('uid'=>$this->uid,'status<'=>1));
		$model->field(array('uid','name','sex','userimg','userimgflag','birthday','birth_year','birthday_flag','provinceid','cityid','rank','ranklevel','views','shares','attention','follow','collect','regtime','sign','signview'));
		$info = $model->find();
		if($info){
			//访问加1
			if($this->uid!=Y_Session::get('uid')){
				$model->where($this->uid)->update('views=views+1');
				Ly_User_Visit::visit($this->uid);
			}
			$web = array('title'=>$info['name'].'的个人主页','keywords'=>$info['sign'],'des'=>$info['sign']);
			$this->assign('uinfo',$info);
			unset($info);
		}else{
			$this->error('对不起，您访问的用户不存在！');
		}
		$this->assign('web',$web);
		$this->display();
	}
	public function addattentionAction(){
		if($this->isAjax()){
			$uid = $this->post('uid');
			if($se = Y_Session::get('uid')){
				if($uid==$se){
					//自己
					echo 6;
				}else{
					if($num=Ly_Friend::addFriend($uid)){
						echo $num;
					}else{
						//失败
						echo 7;
					}
				}
			}else{
				//未登录
				echo 5;
			}
		}
		die;
	}
	//删除关注
	public function delattentionAction(){
		if($this->isAjax()){
			$uid = $this->post('uid');
			if($se=Y_Session::get('uid')){
				if(Ly_Friend::delFriend($uid)){
					echo 2;
				}else{
					echo 3;
				}
			}else{
				echo 1;
			}
		}
		die;
	}
	//发信
	public function sendmsgAction(){
		if($this->isAjax()){
			$uid = $this->post('uid');
			if($se=Y_Session::get('uid')){
				//自己
				if($uid==$se) die(4);
				$content = $this->post('content');
				if($content){
					$num = Ly_Message::send($uid,$content,$se);
					if($num){
						if($num==1){
							//超出
							echo 6;
						}else if($num==3){
							//我的好友
							echo 7;
						}else if($num==4){
							//我的好友和关注我的人
							echo 8;
						}else{
							//成功
							echo 5;
						}
					}else{
						//失败
						echo 3;
					}
				}else{
					//无内容
					echo 2;
				}
			}else{
				//未登录
				echo 1;
			}
		}
		die;
	}
	public function shareAction(){
		if($this->isAjax()){
			if($uid=$this->post('uid')){
				$model = Y_Db::init('content');
				if($uid==Y_Session::get('uid')){
					$where = array('uid'=>$uid,'status<'=>1);
				}else{
					$where = array('uid'=>$uid,'status'=>1);
				}
				$num = $model->where($where)->count();
				$page = $this->get('page')?intval($this->get('page')):1;
				$model->field(array('id','tid','title','img','content','uid','pubtime','views','comments','shares'));
				$model->where($where);
				$model->limit(($page-1)*10,10);
				$model->order('id desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,10);
				$this->assign('pages',$pages->showAjax());
				$this->display(null,false);
				unset($model);
			}
		}
		die;
	}
	//TA的关注
	public function attentionAction(){
		if($this->isAjax()){
			if($uid=$this->post('uid')){
				$model = Y_Db::init('friend');
				$model->where(array('uid'=>$uid));
				$num = $model->count();
				$model->field(array('pubtime','fuid'));
				$model->where(array('z.uid'=>$uid));
				$u = Y_Db::init('user');
				$u->where('l1.uid=z.fuid');
				$u->field(array('uid','name','userimg','follow','sign','signview'));
				$page = $this->get('page')?intval($this->get('page')):1;
				$model->limit(($page-1)*10,10);
				$model->order('z.pubtime desc');
				$this->assign('info',$model->join($u));
				$pages = new Y_Page($num,10);
				$this->assign('pages',$pages->showAjax());
				$this->display(null,false);
			}
		}
		die;
	}
	//粉丝
	public function followAction(){
		if($this->isAjax()){
			if($uid=$this->post('uid')){
				$model = Y_Db::init('friend');
				$model->where(array('fuid'=>$uid));
				$num = $model->count();
				$model->field(array('pubtime'));
				$model->where(array('z.fuid'=>$uid));
				$u = Y_Db::init('user');
				$u->where('l1.uid=z.uid');
				$u->field(array('uid','name','userimg','follow','sign','signview'));
				$page = $this->get('page')?intval($this->get('page')):1;
				$model->limit(($page-1)*10,10);
				$model->order('z.pubtime desc');
				$this->assign('info',$model->join($u));
				$pages = new Y_Page($num,10);
				$this->assign('pages',$pages->showAjax());
				$this->display(null,false);
			}
		}
		die;
	}
	//收集
	/*public function collectAction(){
		if($this->isAjax()){
			if($uid=$this->post('uid')){
				$model = Y_Db::init('collect');
				$model->where(array('uid'=>$uid));
				$num = $model->count();
				$page = $this->get('page')?intval($this->get('page')):1;
				$model->where(array('uid'=>$uid));
				$model->limit(($page-1)*5,5);
				$model->order('collecttime desc');
				$this->assign('info',$model->select());
				$pages = new Y_Page($num,5);
				$this->assign('pages',$pages->showAjax());
				unset($model);
				$this->display(null,false);
			}
		}
		die;
	}*/
	//收集数据
	public function addfavorAction(){
		if($this->isAjax()){
			//先登录
			if(!$data['uid']=Y_Session::get('uid')) die('2');
			if($data['id']=$this->post('id')){
				$type = array('c','w','h');
				$t = strtolower($this->post('t'));
				if($t && in_array($t,$type)){
					$data['type'] = $t;
					$model = Y_Db::init('collect');
					$model->where($data);
					//已经收藏
					if($model->find()) die('3');
					
					//收藏上限
					$rank = Ly_User::getRanklevel($data['uid']);
					$max = Ly_User_Rank::getNum($rank,'collect');
					if($model->where(array('uid'=>$data['uid']))->count()>=$max && $max!=0) die('4');
					$data['collecttime'] = time();
					$model->insert($data);
					$num = $model->where(array('type'=>$data['type'],'id'=>$data['id']))->count();
					if($t=='c'){
						$c = Y_Db::init('content');
					}else if($t=='w'){
						$c = Y_Db::init('wayline');
					}else if($t=='h'){
						$c = Y_Db::init('hotel');
					}
					$c->where($data['id'])->update(array('shares'=>$num));
					//加记录
					$log=Y_Db::init('user');
					$log->where(array('uid'=>$data['uid']))->update('collect=collect+1');
					//成功
					die('0');
				}
			}
		}
		//失败
		die('1');
	}
}
?>
