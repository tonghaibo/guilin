<?php
/**
 * 文章操作表
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-28
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Content{
	//上传插入
	public static function insert($data){
		if($data){
			$model = Y_Db::init('content');
			if($uid=Y_Session::get('uid')){
				$data['uid'] = $uid;
				$data['pubtime'] = time();
				$data['pubip'] = Y_Client::ip();
				return $model->insert($data);
			}
		}
		return false;
	}
	//更新
	public static function update($id,$data){
		if($id and $data){
			$model = Y_Db::init('content');
			return $model->where(array('id'=>$id))->update($data);
		}
		return false;
	}
	//获取所有
	public static function getInfo($id){
		if($id){
			$model = Y_Db::init('content');
			return $model->find($id);
		}
		return false;
	}
	//获取标题
	public static function getTitle($id){
		if($id){
			$model = Y_Db::init('content');
			$name = $model->field(array('title'))->find($id);
			if($name){
				return $name['title'];
			}
		}
		return false;
	}
	//同类推荐
	public static function getSiblings($id,$num=9){
		if($id){
			$tags = Ly_Tags_Bind::getTags(array('cid'=>$id),true);
			if($tags){
				$model = Y_Db::init('tags_bind');
				$model->where('z.tid in('.$tags.') AND z.hid=0 AND z.wid=0 AND z.cid!='.$id.' AND l1.id!=\'\' AND l1.status=1 AND l1.img!=\'\'');
				$model->field('cid');
				$model->limit($num);
				$model->group('z.cid');
				$c = Y_Db::init('content');
				$c->where('l1.id=z.cid');
				$c->field(array('id','title','img','uid'));
				$d = $model->join($c);
				if($d)
					return $d;

			}
		}
		return false;
	}
	//获取推荐
	public static function getHots($num=10){
		$model = Y_Db::init('content');
		$model->field(array('id','uid','title','img'));
		$model->where(array('status'=>1,'img!'=>''));
		$model->limit($num);
		return $model->order('ishot desc,id desc')->select();
	}
	//标签推荐
	public static function getBinds($tid,$id=null,$num=10){
		if($tid){
			$model = Y_Db::init('tags_bind');
			$where = '';
			if($id && is_numeric($id)){
				$where = "z.cid!={$id} AND ";
			}
			$where .= " z.cid!=0 AND z.wid=0 AND z.hid=0 AND l1.status=1 AND l1.img!='' AND z.tid in($tid)";
			$model->where($where);
			$model->field(array('cid'));
			$hotel = Y_Db::init('content');
			$hotel->field(array('id','title','uid','comments','views'));
			$hotel->where("l1.id=z.cid");
			$model->group('z.cid');
			$model->limit($num);
			return $model->join($hotel);
		}
		return false;
	}
}
?>
