<?php
/**
 * Y常用数组操作
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-20
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
class Y_Arr{
	//排序
	public static function sort($list,$filed,$sortby='asc'){
		if(is_array($list)){
			$refer = $resultSet = array();
			foreach ($list as $i => $data)
				$refer[$i] = &$data[$field];
			switch ($sortby) {
				case 'asc': // 正向排序
					asort($refer);
					break;
				case 'desc':// 逆向排序
					arsort($refer);
					break;
				case 'nat': // 自然排序
					natcasesort($refer);
					break;
			}
			foreach ( $refer as $key=> $val)
				$resultSet[] = &$list[$key];
			return $resultSet;
		}
		return false;
	}
	//把数据转化成tree
	public static function tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
		// 创建Tree
		$tree = array();
		if(is_array($list)) {
			// 创建基于主键的数组引用
			$refer = array();
			foreach ($list as $key => $data) {
				$refer[$data[$pk]] =& $list[$key];
			}
			foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId = $data[$pid];
				if ($root == $parentId) {
					$tree[] =& $list[$key];
				}else{
					if (isset($refer[$parentId])) {
						$parent =& $refer[$parentId];
						$parent[$child][] =& $list[$key];
					}
				}
			}
		}
		return $tree;
	}
}
?>
