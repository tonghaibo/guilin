<?php
/**
 * 文件管理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-10-17
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class fileController extends Y_Controller{
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
	public function modAction(){
		$views = P_DIR.'index/views/';
		$widget = P_DIR.'index/widget/';
		$method = $this->get('method');
		if($method=='add'){
			if($this->isAjax()){
				$name = $this->post('name');
				$t = $this->post('t');
				$name = explode('.',$name);
				if(count($name)>1){
					$ext = array_pop($name);
				}
				$name = str_replace('.','',join('.',$name));
				$name = trim($name);
				if($name){
					$name = iconv('utf-8','gbk',$$t.$name);
					if(!file_exists($name.'.'.$ext)){
						if($ext){
							if(file_put_contents($name.'.'.$ext,trim(stripslashes($_POST['content'])))){
								echo 1;
							}
						}else{
							if(@mkdir($name,755)){
								echo 1;
							}
						}
					}
				}
			}
			die;
		}else if($method=='edit'){
			if($this->isAjax()){
				$name = $this->post('name');
				$t = $this->post('t');
				if($name){
					$name = $$t.$name;
					$name = iconv('utf-8','gbk',$name);
					if(file_exists($name)){
						if($data=trim(stripslashes($_POST['content']))){
							if(file_put_contents($name,$data)){
								echo 1;
							}
							die;
						}
						//$content = file_get_contents($name);
						$content = htmlspecialchars(file_get_contents($name));
						echo $content;
					}
				}
			}
			die;
		}else if($method=='del'){
			if($this->isAjax()){
				$name = $this->post('name');
				$t = $this->post('t');
				if($name){
					$name = $$t.$name;
					$name = iconv('utf-8','gbk',$name);
					if($this->post('flag')){
						if(@rmdir($name)){
							echo 1;
							die;
						}
					}
					if(@unlink($name)){
						echo 1;
						die;
					}	
				}
			}
			die;
		}else{
		if($this->isAjax()){
			$t = $this->post('t');
			$path = $_POST['dir'];
			$path = str_replace('.','',$path);
			$path = $$t.$path;
			$data = Y_File::getList($path);
			if($data){
				foreach($data as $val){
					echo '<tr class="list">';
					if($val['type']=='dir'){
						echo '<td><span class="icon icon-dir"></span><a href="javascript:void(0)" onclick="forward(\''.$val['name'].'\',\'views\')" >'.$val['name'].'</a></td>';
						echo '<td>目录</td>';
					}else{
						echo '<td><span class="icon icon-file"></span>'.$val['name'].'</td>';
						echo '<td>文件</td>';
					}	
					echo '<td>'.Y_Pr::size($val['size']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['ctime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['mtime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['atime']).'</td>';
					echo '<td>';
					if($val['type']=='dir'){
						echo '<span class="icon icon-right" title="下一级" onclick="forward(\''.$val['name'].'\',\''.$t.'\')"></span>';
						if($val['size']==0){
							echo '<span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\',\''.$t.'\',true)" ></span>';
						}
						
					}else{
						echo '<span class="icon icon-edit" title="编辑" onclick="edit(\''.$val['name'].'\',\''.$t.'\')" ></span><span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\',\''.$t.'\')" ></span>';
					}
					echo '</td>';
					echo '</tr>';
				}
			}
			die;
		}
		//时间
		@set_time_limit(600);
		//内存
		@ini_set('memory_limit','32M');
		$views = Y_File::getList($views);
		$widget = Y_File::getList($widget);
		$this->assign('views',$views);
		$this->assign('widget',$widget);
		$this->display();
		}
	}
	//缓存管理
	public function cacheAction(){
		$dir = P_DIR.'index/html/';
		$method = $this->get('method');
		if($method=='del'){
			if($this->isAjax()){
				if($this->post('all')){
					Y_File::delete($dir);
					echo 1;
					die;
				}
				$name = trim($this->post('name'));
				if($name){
					$name = $dir.$name;
					$name = iconv('utf-8','gbk',$name);
					if($this->post('flag')){
						if(@rmdir($name)){
							echo 1;
							die;
						}
					}
					if(@unlink($name)){
						echo 1;
						die;
					}	
				}
			}
			die;
		}
		if($this->isAjax()){
			$path = $_POST['dir'];
			$path = str_replace('.','',$path);
			$data = Y_File::getList($dir.$path);
			if($data){
				foreach($data as $val){
					echo '<tr class="list">';
					if($val['type']=='dir'){
						echo '<td><span class="icon icon-dir"></span><a href="javascript:void(0)" onclick="forward(\''.$val['name'].'\')" >'.$val['name'].'</a></td>';
						echo '<td>目录</td>';
					}else{
						echo '<td><span class="icon icon-file"></span><a href="'.$dir.$path.$val['name'].'" target="_blank">'.$val['name'].'</a></td>';
						echo '<td>文件</td>';
					}
					
					echo '<td>'.Y_Pr::size($val['size']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['ctime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['mtime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['atime']).'</td>';
					echo '<td>';
					if($val['type']=='dir'){
						echo '<span class="icon icon-right" title="下一级" onclick="forward(\''.$val['name'].'\')"></span>';
						if($val['size']==0){
							echo '<span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\',true)" ></span>';
						}
						
					}else{
						echo '<span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\',\''.$t.'\')" ></span>';
					}
					echo '</td>';
					echo '</tr>';
					
				}
			}
			die;
		}
		$this->assign('html',Y_File::getList($dir));
		$this->display();
	}
	//附件管理
	public function uploadsAction(){
		$method = $_GET['method'];
		if($method=='pass'){
			if($id = $this->get('id')){
				$data['status'] = intval($this->get('v'));
				$model = Y_Db::init('uploads');
				$model->where($id);
				if($model->update($data)){
					$this->success('更新附件状态成功！');
				}else{
					$this->error('更新附件状态失败！');
				}
			}else{
				$this->error('请传入ID值！');
			}
		}
		if($method=='del'){
			if(Ly_Uploads::delete($_GET['id'])){
				$this->success('删除附件成功！');
			}else{
				$this->error('删除附件失败！');
			}
		}
		$where = 'true';
		
		if($uid=$this->get('s_uid')){
			$where .= " AND uid='{$uid}'";
		}

		if($sys=$this->get('s_issys')){
			$where .=" AND issys=1";
		}
		if(isset($_GET['s_status'])){
			$status = join($_GET['s_status'],',');
			$where .= " AND status in({$status})";
		}
		//puttime
		if($t1=$this->get('s_pubtime1')){
			$t1 = strtotime($t1);
			$where .= " AND uptime>='{$t1}'";
		}
		if($t2=$this->get('s_pubtime2')){
			$t2 = strtotime($t2);
			$where .= " AND uptime<='{$t2}'";
		}
		if($id=$this->get('s_id')){
			$where =" id='{$id}'";
		}
		$page = intval($this->get('page'));
		$page = $page?$page:1;
		$model = Y_Db::init('uploads');
		$model->where($where);
		$count = $model->count();
		$model->limit(($page-1)*15,15);
		$model->order('id desc');
		$model->where($where);
		$this->assign('ulist',$model->select());
		$page = new Y_Page($count,15);
		$this->assign('pages',$page->show());
		$this->display();
	}
	//语言包管理
	public function langAction(){
		$dir = P_DIR.'language/';
		$method = $this->get('method');
		if($method=='edit'){
			if($this->isAjax()){
				$name = $this->post('name');
				if($name){
					$name = $dir.$name;
					$name = iconv('utf-8','gbk',$name);
					if(file_exists($name)){
						if($data=trim(stripslashes($_POST['content']))){
							if(file_put_contents($name,$data)){
								echo 1;
							}
							die;
						}
						$content = file_get_contents($name);
						//$content = htmlspecialchars(file_get_contents($name));
						echo $content;
					}
				}
			}
			die;
		}
		if($this->isAjax()){
			$data = Y_File::getList($dir);
			if($data){
				foreach($data as $val){
					echo '<tr class="list">';
					echo '<td>'.($val['type']=='dir'?'<span class="icon icon-dir"></span>':'<span class="icon icon-file"></span>').$val['name'].'</td>';
					echo '<td>'.($val['type']=='dir'?'目录':'文件').'</td>';
					echo '<td>'.Y_Pr::size($val['size']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['ctime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['mtime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['atime']).'</td>';
					echo '<td>';
					echo '<span class="icon icon-edit" title="编辑" onclick="edit(\''.$val['name'].'\')" ></span>';
					echo '</td>';
					echo '</tr>';
					
				}
			}
			die;
		}
		$this->assign('html',Y_File::getList($dir));
		$this->display();
	}
	public function pubAction(){
		$dir = P_DIR.'public/';
		$this->assign('dir',$dir);
		$method = $this->get('method');
		if($method=='add'){
			$dir = $dir.$_POST['dir'];
			if(Y_Upload::upfile('file',$dir)){
				$this->success('新增文件成功');
			}else{
				$this->error('新增文件失败！');
			}
			die;
		}else if($method=='edit'){
			if($this->isAjax()){
				$name = $this->post('name');
				if($name){
					$name = $dir.$name;
					$name = iconv('utf-8','gbk',$name);
					if(file_exists($name)){
						if($data=trim(stripslashes($_POST['content']))){
							if(file_put_contents($name,$data)){
								echo 1;
							}
							die;
						}
						$content = file_get_contents($name);
						//$content = htmlspecialchars(file_get_contents($name));
						echo $content;
					}
				}
			}
			die;
		}else if($method=='del'){
			if($this->isAjax()){
				$name = $this->post('name');
				if($name){
					$name = $dir.$name;
					$name = iconv('utf-8','gbk',$name);
					if($this->post('flag')){
						if(@rmdir($name)){
							echo 1;
							die;
						}
					}
					if(@unlink($name)){
						echo 1;
						die;
					}	
				}
			}
			die;
		}else{
		if($this->isAjax()){
			$path = $_POST['dir'];
			$path = str_replace('.','',$path);
			$path = $dir.$path;
			$data = Y_File::getList($path);
			if($data){
				foreach($data as $val){
					echo '<tr class="list">';
					if($val['type']=='dir'){
						echo '<td><span class="icon icon-dir"></span><a onclick="forward(\''.$val['name'].'\')" href="javascript:void(0)">'.$val['name'].'</a></td>';
						echo '<td>目录</td>';
					}else{
						echo '<td>'.(Y_File::isFile($val['ext'])?'<span class="icon icon-file"></span>':(Y_File::isImage($val['ext'])?'<span class="icon icon-img"></span>':'<span class="icon icon-ask"></span>')).$val['name'].'</td>';
						echo '<td>'.(Y_File::isOk($val['ext'])?$val['ext']:'<span class="cred">未知</span>').'</td>';
					}
					echo '<td>'.Y_Pr::size($val['size']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['ctime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['mtime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['atime']).'</td>';
					echo '<td>';
					if($val['type']=='dir'){
					echo '<span class="icon icon-right" title="下一级" onclick="forward(\''.$val['name'].'\')"></span>';
					if($val['size']==0){
						echo '<span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\',true)" ></span>';
					}
					}else{
					if(Y_File::isFile($val['ext'])){
						echo '<span class="icon icon-edit" title="编辑" onclick="edit(\''.$val['name'].'\')"></span>';
					}else if(Y_File::isImage($val['ext'])){
						echo '<span class="icon icon-img" titel="查看" onclick="view(\''.$val['name'].'\')"></span>';
					}
					echo '<span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\')" ></span>';

					}
					echo '</td>';
					echo '</tr>';					
				}
			}
			die;
		}
		$views = Y_File::getList($dir);
		$this->assign('list',$views);
		$this->display();
		}
	}
	//运行记录
	public function runAction(){
		$dir = P_DIR.'runtime/';
		$this->assign('dir',$dir);
		$method = $this->get('method');
		if($method=='add'){
			$dir = $dir.$_POST['dir'];
			if(Y_Upload::upfile('file',$dir)){
				$this->success('新增文件成功');
			}else{
				$this->error('新增文件失败！');
			}
			die;
		}else if($method=='view'){
			if($this->isAjax()){
				$name = $this->post('name');
				if($name){
					$name = $dir.$name;
					$name = iconv('utf-8','gbk',$name);
					if(file_exists($name)){
						$content = file_get_contents($name);
						//$content = htmlspecialchars(file_get_contents($name));
						echo $content;
					}
				}
			}
			die;
		}else if($method=='del'){
			if($this->isAjax()){
				$name = $this->post('name');
				if($name){
					$name = $dir.$name;
					$name = iconv('utf-8','gbk',$name);
					if($this->post('flag')){
						if(@rmdir($name)){
							echo 1;
							die;
						}
					}
					if(@unlink($name)){
						echo 1;
						die;
					}	
				}
			}
			die;
		}else{
		if($this->isAjax()){
			$path = $_POST['dir'];
			$path = str_replace('.','',$path);
			$path = $dir.$path;
			$data = Y_File::getList($path);
			if($data){
				foreach($data as $val){
					echo '<tr class="list">';
					echo '<td>'.($val['type']=='dir'?'<span class="icon icon-dir"></span>':(Y_File::isFile($val['ext'])?'<span class="icon icon-file"></span>':(Y_File::isImage($val['ext'])?'<span class="icon icon-img"></span>':'<span class="icon icon-ask"></span>'))).$val['name'].'</td>';
					echo '<td>'.($val['type']=='dir'?'目录':(Y_File::isOk($val['ext'])?$val['ext']:'<span class="cred">未知</span>')).'</td>';
					echo '<td>'.Y_Pr::size($val['size']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['ctime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['mtime']).'</td>';
					echo '<td>'.date('Y-m-d H:i:s',$val['atime']).'</td>';
					echo '<td>';
					if($val['type']=='dir'){
					echo '<span class="icon icon-right" title="下一级" onclick="forward(\''.$val['name'].'\')"></span>';
					if($val['size']==0){
						echo '<span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\',true)" ></span>';
					}
					}else{
					if(Y_File::isFile($val['ext'])){
						echo '<span class="icon icon-more" title="编辑" onclick="view(\''.$val['name'].'\')"></span>';
					}
					echo '<span class="icon icon-del" title="删除" onclick="del(\''.$val['name'].'\')" ></span>';

					}
					echo '</td>';
					echo '</tr>';					
				}
			}
			die;
		}
		$views = Y_File::getList($dir);
		$this->assign('list',$views);
		$this->display();
		}
	}
}
?>
