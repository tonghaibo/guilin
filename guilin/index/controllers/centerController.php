<?php
/**
 * 帮助中心
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-05
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class centerController extends Y_Controller{
	//
	public function __construct(){
		$web = Y_Db::init('seos');
		$str = Y::$controller.'_'.Y::$action;
		$web->where(array('name'=>$str));
		$web->field(array('title','author','keywords','des'));
		$this->assign('web',$web->find());
	}
	public function indexAction(){
		$this->Go('center_about.html');
	}
	public function aboutAction(){
		$this->display();
	}
	//联系我们
	public function contactusAction(){
		$this->display();
	}
	//广告服务
	public function adsAction(){
		$this->display();
	}
	//人才招聘
	public function jobinfoAction(){
		$this->display();
	}
	//客服中心
	public function customerAction(){
		$this->display();
	}
	//网站地图
	public function privacyAction(){
		$this->display();
	}
	//使用必读
	public function helpAction(){
		$this->display();
	}
}
?>
