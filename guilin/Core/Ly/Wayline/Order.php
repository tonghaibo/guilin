<?php
/**
 * 订单
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-11-17
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y'))die;
class Ly_Wayline_Order{
	public static function getCount($wid){
		if($wid){
			$model = Y_Db::init('wayline_order');
			return $model->where(array('wid'=>$wid))->count();
		}
		return false;
	}
}
?>
