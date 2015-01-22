<?php
/**
 * 前台入口文件
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-04
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
define('IN_Y',true);
define('APP','./index/');
define('Y_ROOT','./Core/Y/');
//是否开启缓存
define('Y_CACHE',false);
//缓存时间以分为单位20即20分钟
define('Y_CACHETIME',20);
//是否开启调试 不写也是关闭
define('Y_DEBUG',true);
//session保存方式 Db,File,不写则默认或者不定义
//定义db需要指定保存的表名
define('Y_SESSION_TAB','session');
define('Y_SESSION','Db');
//加载入口文件
require(Y_ROOT.'Y.php');
?>
