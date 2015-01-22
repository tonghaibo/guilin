<?php
/**
 * 后台系统管理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-10
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class sysController extends Y_Controller{
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
	//权限添加
	public function pointsAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($this->post('name')){
				$data['name'] = $this->post('name');
				$data['fid'] = $this->post('fid');
				$data['control'] = $this->post('control');
				$data['action'] = $this->post('action');
				$data['method'] = $this->post('method');
				$data['sort'] = $this->post('sort');
				$data['ispublic'] = $this->post('ispublic');
				$data['isview'] = $this->post('isview');
				$data['des'] = $this->post('des');
				if(Ly_Points::insert($data)){
					$this->success('添加节点成功!','',3);
					die;
				}else{
					$this->error('添加节点失败!','',3);
					die;
				}
			}
			$list = Ly_Points::getList();
			$this->assign('plist',$list);
			$this->display('points/add',false);
		}else if($method=="edit"){
			$id = $this->get('id');
			if(!$id){
				$this->error('ID号不能不为空!','',3);
				die;
			}
			if($this->post('name')){
				$data['name'] = $this->post('name');
				$data['fid'] = $this->post('fid');
				$data['control'] = $this->post('control');
				$data['action'] = $this->post('action');
				$data['method'] = $this->post('method');
				$data['sort'] = $this->post('sort');
				$data['ispublic'] = $this->post('ispublic');
				$data['isview'] = $this->post('isview');
				$data['des'] = $this->post('des');
				if(Ly_Points::update($data,$this->get('id'))){
					$this->success('更新成功！','',3);
					die;
				}else{
					$this->error('更新失败！请返回','',3);
					die;
				}
			}
			$this->assign('pinfo',Ly_Points::info($id));
			$this->assign('info',0);
			$this->assign('plist',Ly_Points::getList());
			$this->display('points/edit',false);
		}else if($method=="ajax"){
			if($this->isAjax()){
				$id = $this->post('id');
				if($id){
					$data = Ly_Points::getLists($id);
					if($data){
						echo json_encode($data);
						die;
					}
				}
			}
		}else if($method=="del"){
			$id = $this->get('id');
			$data = Ly_Points::delete($id);
			if($data){
				$this->success('删除节点成功!');
			}else{
				$this->error('删除节点失败！');
			}
		}else{
			$this->assign('plist',Ly_Points::getLists(0));
			$this->display('points',false);
		}
	}
	//组员添加
	public function groupAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['gname']=$this->post('gname')){
				$data['power'] = join(',',$_POST['power']);
				$data['issuper'] = $this->post('isSuper');
				if(Ly_Group::insert($data)){
					$this->success('添加新组成功!');
					die;
				}else{
					$this->error('添加新组失败！');
					die;
				}
			}else{
				$this->assign('plist',Ly_Points::getList());
				$this->display('group/add',false);
			}
		}else if($method=='del'){
			$this->display('group',false);
		}else if($method=='edit'){
			$gid = $this->get('gid');
			if(!$gid){
				$this->error('ID不能为空！','',3);
				die;
			}
			if($data['gname']=$this->post('gname')){
				$data['power'] = join(',',$_POST['power']);
				$data['issuper'] = $this->post('isSuper');
				$data['leaderid'] = $this->post('leaderid');
				$data['members'] = join(',',$_POST['members']);
				if(Ly_Group::update($gid,$data)){
					$this->success('修改组信息成功！','',3);
					die;
				}else{
					$this->error('修改组信息失败！','',3);
					die;
				}
			}
			$info = Ly_Group::getInfo($gid);
			if(!$info){
				$this->error('传入组ID无效！','',3);
				die;
			}
			$this->assign('plist',Ly_Points::getList());
			$this->assign('ginfo',$info);
			$this->assign('members',Ly_Admin::getNames($info['members']));
			$this->display('group/edit',false);
		}else{
			$this->assign('glist',Ly_Group::getList());
			$this->display('group',false);
		}
	}
	//组员管理
	public function adminAction(){
		$method = $this->get('method');
		if($method=='edit'){
			if(!$id=$this->get('id')){
				$this->error('用户ID传入有误！');
				die;	
			}
			if($data['name']=$this->post('name')){
				$data['realname'] = $this->post('realname');
				$data['flag'] = $this->post('flag');
				$data['groupid'] = $this->post('groupid');
				if(Ly_Admin::update($id,$data)){
					//更新组员
					Ly_Group::upMembers($data['groupid'],$id);
					$this->success('修改管理员成功','',3);
					die;
				}else{
					$this->error('修改管理员失败','',3);
					die;
				}
			}
			if($info=Ly_Admin::getInfo($id)){
				$this->assign('ainfo',$info);
				$this->assign('glist',Ly_Group::getList());
				$this->display('admin/edit',false);
			}else{
				$this->error('该用户不存在！');
			}
		}else if($method=='del'){
			$id = $this->get('id');
			if(Ly_Admin::delete($id)){
				$this->success('删除管理员成功！');
			}else{
				$this->error('删除管理员失败！');
			}
		}else if($method=='pass'){
			if(!$id=$this->get('id')){
				$this->error('用户ID传入有误！');
				die;	
			}
			if($data['password']=$this->post('password')){
				$data['password'] = md5($data['password']);
				if(Ly_Admin::update($id,$data)){
					$this->success('修改管理员密码成功','',3);
					die;
				}else{
					$this->error('修改管理员密码失败','',3);
					die;
				}
			}
			$this->display('admin/pass',false);
		}else if($method=='add'){
			if($data['name']=$this->post('name')){
				$data['realname'] = $this->post('realname');
				$data['password'] = md5($this->post('password'));
				$data['flag'] = $this->post('flag');
				$data['groupid'] = $this->post('groupid');
				if($uid=Ly_Admin::insert($data)){
					//更新组员
					Ly_Group::upMembers($data['groupid'],$uid);
					$this->success('添加新管理员成功','',3);
					die;
				}else{
					$this->error('添加新管理员失败','',3);
					die;
				}
			}
			$this->assign('glist',Ly_Group::getList());
			$this->display('admin/add',false);
		}else{
			$this->assign('alist',Ly_Admin::getList());
			$this->display('admin',false);
		}
	}
	//选项设置
	public function optionsAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['itemsname']=$this->post('itemsname')){
				$data['tabname'] = $this->post('tabname');
				$data['colname'] = $this->post('colname');
				$data['items'] = $this->post('items');
				if(Ly_Items::insert($data)){
					$this->success('添加选项成功!');
					//die;
				}else{
					$this->error('添加选项失败！');
					//die;
				}
			}
			$this->display('options/add',false);
		}else if($method=='edit'){
			if(!$id=$this->get('id')){
				$this->error('传入ID不能为空');
			}
			if($data['itemsname']=$this->post('itemsname')){
				$data['tabname'] = $this->post('tabname');
				$data['colname'] = $this->post('colname');
				$data['items'] = $this->post('items');
				if(Ly_Items::update($id,$data)){
					$this->success('添加选项修改成功!');
					//die;
				}else{
					$this->error('添加选项修改失败！');
					//die;
				}
			}
			if($info=Ly_Items::getInfo($id)){
				$this->assign('iinfo',$info);
				$this->display('options/edit',false);
			}else{
				$this->error('传入选项ID值无效！');
			}
		}else if($method=='view'){
			if($this->isAjax()){
				$id = $this->get('id');
				$name = Ly_Items::getItems($id);
				if($name){
					echo $name;
				}
				die;	
			}
		}else{
			$page = intval($this->get('page'));
			$page = $page?$page:1;
			$model = Y_Db::init('items');
			$count = $model->count();
			$model->field(array('itemsid','itemsname','tabname','colname'));
			$model->order('itemsid DESC');
			$model->limit(($page-1)*15,15);
			$this->assign('llist',$model->select());
			//分页
			$page = new Y_Page($count,15);
			$this->assign('pages',$page->show());
			$this->display('options',false);
		}
	}
	//seo
	public function seoAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['name']=$this->post('name')){
				$data['title'] = $this->post('title');
				$data['author'] = $this->post('author');
				$data['keywords'] = $this->post('keywords');
				$data['des'] = $this->post('des');
				if(Ly_Seos::insert($data)){
					$this->success('添加新的SEO模版成功！');

				}else{
					$this->error('添加新的SEO模版失败！');
				}
			}
			$this->display('seo/add');
			die;
		}
		if($method=='edit'){
			$name = $this->get('name');
			if($info=Ly_Seos::getInfo($name)){
				if($data['name']=$this->post('name')){
					$data['title'] = $this->post('title');
					$data['author'] = $this->post('author');
					$data['keywords'] = $this->post('keywords');
					$data['des'] = $this->post('des');
					if(Ly_Seos::update($name,$data)){
						$this->success('修改SEO页面成功！');

					}else{
						$this->error('修改SEO模版失败！');
					}
				}
				$this->assign('sinfo',$info);
				$this->display('seo/edit');
			}else{
				$this->error('传入名称有误！');
			}
			die;
		}
		if($method=='del'){
			$name = $this->get('name');
			if(Ly_Seos::delete($name)){
				$this->success('删除成功！');
			}else{
				$this->error('删除失败！');
			}
		}
		$where = 'true';
		if($name=$this->get('name')){
			$where = ' name like \''.$name.'%\'';
		}
		$model = Y_Db::init('seos');
		$page = intval($this->get('page'));
		$page = $page?$page:1;
		$model->where($where);
		$count = $model->count();
		$model->limit(($page-1)*15,15);
		$this->assign('slist',$model->where($where)->select());
		//分页
		$page = new Y_Page($count,15);
		$this->assign('pages',$page->show());
		$this->display();
	}
	//邮件模版
	public function tplAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($data['subject']=$this->post('subject')){
				$data['content'] = stripslashes($this->post('des',false));
				$data['tpltype'] = $this->post('tpltype');
				$data['flag'] = $this->post('flag');
				if(Ly_Mail::insert($data)){
					$this->success('添加新的模版成功！');
				}else{
					$this->error('添加新的模版失败！');
				}
			}
			$this->display('tpl/add');
			die;
		}
		if($method=='edit'){
			$id = $this->get('id');
				if($info=Ly_Mail::getInfo($id)){
				if($data['subject']=$this->post('subject')){
					$data['content'] = stripslashes($this->post('des',false));
					$data['tpltype'] = $this->post('tpltype');
					$data['flag'] = $this->post('flag');
					if(Ly_Mail::update($id,$data)){
						$this->success('修改模版成功！');
					}else{
						$this->error('修改模版失败！');
					}
				}
				$this->assign('info',$info);
				$this->display('tpl/edit');
			}else{
				$this->error('传入ID有误！或者该模版已经不存在！');
			}
			die;
		}
		$model = Y_Db::init('mailtpl');
		$page = intval($this->get('page'));
		$page = $page?$page:1;
		$model->where($where);
		$count = $model->count();
		$model->limit(($page-1)*15,15);
		$this->assign('slist',$model->where($where)->select());
		//分页
		$page = new Y_Page($count,15);
		$this->assign('pages',$page->show());
		$this->display();
	}
	//日志
	public function logAction(){
		$method=$this->get('method');
		if($method=='list'){
			if($this->isAjax()){
				$file = $this->get('file');
				$data = Y_Log::getList($file);
				$str = '';
				if($data){
					$data = array_reverse($data);
					foreach($data as $val){
						$str .= '<option value="'.$val.'">'.$val.'</option>';
					}
				}
				echo $str;
			}
			die;
		}
		if($this->isAjax()){
			$file = $this->get('file');
			$date = $this->get('d');
			if($date==''){
				$date = date('Ym',time());
			}
			$fi = $date.'_'.$file.'.php';
			$data = Y_Log::read($fi);
			$str = '';
			$page = '';
			if($data){
				$data = array_reverse($data);
				$num = count($data);
				$page = new Y_Page($num,20);
				$page = $page->showAjax();
				$page = '<tfoot><tr><td style="text-align:right" colspan="10"><div id="ui-page">'.$page.'</td></div></tr></tfoot>';
				$pagenum = $this->get('page');
				$pagenum = $pagenum?$pagenum:1;
				$i = ($pagenum-1)*20;
				$c = $i+20;
				switch($file){
					case 'sys':
						$str .="<thead><tr><th>级别</th><th>访问账号</th><th>操作模块</th><th>访问时间</th><th>访问URL</th><th>访问IP</th></tr></thead><tbody>";
						for($i;$i<$c;$i++){
							if($data[$i]){
							$str .= '<tr>';
							$str .= '<td class="c_'.$data[$i][0].'"></span>'.$data[$i][0].'</td>';							
							$str .= '<td>'.$data[$i][2].'</td>';
							$str .= '<td>'.$data[$i][3].'</td>';
							$str .= '<td>'.date('Y/m/d H:i:s',$data[$i][1]).'</td>';
							$str .= '<td><a href="javascript:void(0)" onClick="showContent(\''.$data[$i][4].'\')" >'.Y_Pr::substr($data[$i][4],0,50).'</a></td>';
							$str .= '<td>'.$data[$i][5].'</td>';
							$str .= '</tr>';
							}
						}
						$str .= '</tbody>';
						break;
					case 'sql':
						$str .="<thead><tr><th>级别</th><th>信息提示</th><th>访问时间</th><th>访问IP</th></tr></thead><tbody>";
						for($i;$i<$c;$i++){
							if($data[$i]){
							$str .= '<tr>';
							$str .= '<td class="c_'.$data[$i][0].'"></span>'.$data[$i][0].'</td>';
						$str .= '<td><a href="javascript:void(0)" onClick="showContent(\''.str_replace('\'','',$data[$i][2]).'\')" >'.Y_Pr::substr($data[$i][2],0,80).'</a></td>';
							$str .= '<td>'.date('Y/m/d H:i:s',$data[$i][1]).'</td>';
							$str .= '<td>'.$data[$i][3].'</td>';
							$str .= '</tr>';
							}
						}
						$str .= '</tbody>';
						break;
					case 'error':
						$str .="<thead><tr><th>级别</th><th>信息提示</th><th>访问时间</th><th>访问IP</th></tr></thead><tbody>";
						for($i;$i<$c;$i++){
							if($data[$i]){
							$str .= '<tr>';
							$str .= '<td class="c_'.$data[$i][0].'"></span>'.$data[$i][0].'</td>';
							$str .= '<td><a href="javascript:void(0)" onClick="showContent(\''.$data[$i][2].'\')" >'.Y_Pr::substr($data[$i][2],0,80).'</a></td>';
							$str .= '<td>'.date('Y/m/d H:i:s',$data[$i][1]).'</td>';
							$str .= '<td>'.$data[$i][3].'</td>';
							$str .= '</tr>';
							}
						}
						$str .= '</tbody>';
						break;
					case 'mail':
						$str .="<thead><tr><th>级别</th><th>状态</th><th>邮件类别</th><th>发送时间</th><th>收件邮箱</th><th>访问IP</th></tr></thead><tbody>";
						for($i;$i<$c;$i++){
							if($data[$i]){
							$str .= '<tr>';
							$str .= '<td class="c_'.$data[$i][0].'"></span>'.$data[$i][0].'</td>';
							$str .= '<td>'.$data[$i][2].'</td>';
							$str .= '<td>'.$data[$i][3].'</td>';
							$str .= '<td>'.date('Y/m/d H:i:s',$data[$i][1]).'</td>';
							$str .= '<td><a href="?ro=member&ac=member&semail='.$data[$i][4].'">'.$data[$i][4].'</a></td>';
							$str .= '<td>'.$data[$i][5].'</td>';
							$str .= '</tr>';
							}
						}
						$str .= '</tbody>';
						break;
					case 'syslogin':
						$str .="<thead><tr><th>级别</th><th>访问账号</th><th>信息提示</th><th>访问时间</th><th>访问IP</th></tr></thead><tbody>";
						for($i;$i<$c;$i++){
							if($data[$i]){
							$str .= '<tr>';
							$str .= '<td class="c_'.$data[$i][0].'"></span>'.$data[$i][0].'</td>';
							$str .= '<td>'.$data[$i][2].'</td>';
							$str .= '<td>'.$data[$i][3].'</td>';
							$str .= '<td>'.date('Y/m/d',$data[$i][1]).'</td>';
							$str .= '<td>'.$data[$i][4].'</td>';
							$str .= '</tr>';
							}
						}
						$str .= '</tbody>';
						break;
				
				}
			}
			if($str==''){
				$str = '<tr><td>无任何记录</td></tr>';
			}
			unset($data);
			$str .=$page;
			echo $str;
			die;
		}
		$this->display();
	}
	//表
	public function tabAction(){
		$method= $this->get('method');
		if($method=='view'){
			if($this->isAjax()){
				$name = $this->get('tab');
				if($name){
					$model = Y_Db::init($name);
					$col = $model->descTable();
					$index = $model->indexTable();
					if($col){
						foreach($col as $k=>$v){
							$s[] = $v;
						}
						echo json_encode(array('col'=>$s,'index'=>$index));
					}
				}
			}
			die;
		}else if($method=='do'){
			$name = $this->get('name');
			if($name=='all'){
				$model = Y_Db::init();
				$tables = $model->showTables();
				foreach($tables as $v){
					$model = Y_Db::init(str_replace(Y::Config('db','prefix'),'',$v));
					$model->doTable('optimize');
				}
				$this->success('优化整个数据库表成功！');
			}
			$model = Y_Db::init($name);
			if($model->doTable('optimize')){
				$this->success('优化表'.$name.'成功！');
			}else{
				$this->error('优化表'.$name.'失败！');
			}
			die;
		}else if($method=='bak'){
			$model = Y_Db::init();
			$h1  = '--  Y-Famework '."\r\n";
			$h1 .= '--  yuexinok@126.com'."\r\n";
			$h1 .= "--  生成日期：".date('Y年m月d日',time())."\r\n";
			$h1 .= "--  MySQL服务器版本：".$model->dbVersion()."\r\n";
			$h1 .= "--  PHP版本：".PHP_VERSION."\r\n";
			$h1 .= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";'."\r\n";
			$name = $this->get('name');
			if($name=='all'){
				$name = Y::Config('db','dbname');
				$h1 .= "-- 数据库：$name\r\n";
				$tables = $model->showTables();
				foreach($tables as $v){
					$model = Y_Db::init(str_replace(Y::Config('db','prefix'),'',$v));
					$h1 .= $model->exportTable();
				}
			}else if($name){
				$model = Y_Db::init($name);
				$name = Y::Config('db','prefix').$name;
				$h1 .= $model->exportTable();	
			}
			$name = $name.'_'.date('Ymd').'.sql';
			header('Content-Encoding: none');
			header("Content-type:application/octet-stream");
    			header("Content-disposition:attachment;filename=".$name);	
			header("Accept-Ranges:bytes");
			echo $h1;
			exit;
			
		}else{
			$table = Y_Db::init('admin');
			$table = $table->listTable();
			$this->assign('list',$table);
			$this->display();	
		}
	}
	//公告
	public function noticeAction(){
		$method = $this->get('method');
		if($method=='add'){
			if($this->isAjax()){
				$uid = $this->get('uid');
				$name = Ly_User::getName($uid);
				echo $name?'<a href="govern.php?ro=member&ac=member&s_uid='.$uid.'" target="_blank">'.$name.'</a>':'<span class="cred">未检测到</span>';
				die;
			}
			if($data['title']=$this->post('title')){
				$data['url'] = $this->post('url');
				$data['uid'] = $this->post('uid');
				$data['notice'] = $this->post('notice');
				$data['pubtime'] = time();
				$data['status'] = intval($this->post('status'));
				$model = Y_Db::init('notice');
				if($model->insert($data)){
					$this->success('添加公告成功！');
				}else{
					$this->error('添加公告失败！');
				}
			}
			$this->display('notice/add');
		}else if($method=='edit'){
			$id = $this->get('id');
			if(!$id) $this->error('错误参数！');
			$model = Y_Db::init('notice');
			$model->where(array('id'=>$id));
			$info = $model->find();
			if(!$info) $this->error('没有找到该通知！');
			if($data['title']=$this->post('title')){
				$data['url'] = $this->post('url');
				$data['uid'] = $this->post('uid');
				$data['notice'] = $this->post('notice');
				$data['status'] = intval($this->post('status'));
				if($model->where(array('id'=>$id))->update($data)){
					$this->success('修改公告成功！');
				}else{
					$this->error('修改公告失败！');
				}
			}
			$this->assign('info',$info);
			$this->display('notice/edit');
		}else if($method=='del'){
			$id = $this->get('id');
			$model = Y_Db::init('notice');
			if($model->where(array('id'=>$id))->delete()){
				$this->success('删除成功！');
			}else{
				$this->success('删除失败！');
			}
		}else if($method=='do'){
			$id = $this->get('id');
			$s = intval($this->get('s'));
			$model = Y_Db::init('notice');
			$model->where("id in({$id})");
			$model->update(array('status'=>$s));
			$this->success('操作成功！');
		}else{
			$id = $this->get('id');
			$uid = $this->get('uid');
			$title = $this->get('title');
			$where = "true";
			if($uid) $where .= " AND　uid={$uid}";
			if($title) $where .= " AND　title like '%{$title}%'";
			if($id) $where = "id={$id}";
			$page = $this->get('page')?intval($this->get('page')):1;
			$model = Y_Db::init('notice');
			$num=$model->where($where)->count();
			$model->where($where)->order('id desc');
			$model->limit(($page-1)*10,10);
			$this->assign('info',$model->select());
			$pages = new Y_Page($num,10);
			$this->assign('pages',$pages->show());
			$this->display();		
		}
	}
}
?>
