<?php
/**
 * 广告配置文件
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-12-22
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die();
return array(
	//首页轮播广告 图片统一存放在uploads/ads/下面 大小663X310
	'index'=>array(
		array('url'=>'index.html','title'=>'发菜谱 摘苹果 赢大奖！没有啊是哈','des'=>'好豆发菜谱，赢豆币奖励？这个金秋，发菜谱，还能摘苹果，赢超级大奖啦！红彤彤的诱惑里，深藏着一份怎样的神秘大奖呢？心动了吧？还等什么，赶快发布菜谱参与活动吧！','img'=>'1.jpg'),
		array('url'=>'map.html','title'=>'秋季，七类食物帮你抗炎','des'=>'秋季气候干燥，天气冷，容易产生各种疾病，很多疾病的根源之一就是体内炎症。饮食抗炎对预防疾病至关重要。在这天气凉爽食欲大开的阶段吃些健康的食品能预防各种病症萌生。','img'=>'2.jpg'),
		array('url'=>'share.html','title'=>'铁板也疯狂@赤坂亭','des'=>'赤坂亭，上海滩老牌的铁板烤肉，相信无人不知无人不晓，分店也如雨后春笋般地冒不停，五角场新开分店，买四送二的特惠促销，吸引了众多的目光，大好机会怎样错过？！','img'=>'3.jpg'),
		array('url'=>'hotel.html','title'=>'蚝油三丝','des'=>'杏鲍菇菌肉质嫩，被称为“平菇王”，具有杏仁的清香和鲍鱼的滑嫩口感。蚝油味道鲜美、蚝香浓郁，把杏鲍菇、耗油、味极鲜酱油组合在一起，“三鲜合一”，味道真的是很鲜！','img'=>'4.jpg'),
		array('url'=>'wayline.html','title'=>'充满异域风情的秋日暖汤：八分钟快捷泰式南瓜羹','des'=>'今天为大家推荐的这款泰式南瓜羹，是有别于一般甜口南瓜羹的做法，加了咖喱酱的南瓜，甜中还带着丝丝咖喱特有的辛辣，不仅可以让人食欲大增，落入胃中之后，还会为身体带来阵阵暖意，在这秋天里饮用再合适不过了。','img'=>'5.jpg')
	),
	//首页精彩图集 图片统一存放在uploads/imgs/下面 大小195x125
	'imgs'=>array(
		array('url'=>'share_detail.html?id=1','title'=>'月亮岛','img'=>'10.jpeg'),
		array('url'=>'share_detail.html?id=2','title'=>'雷锋坛','img'=>'11.jpeg'),
		array('url'=>'share_detail.html?id=3','title'=>'夜色','img'=>'12.jpeg'),
		array('url'=>'share_detail.html?id=4','title'=>'钟烈师','img'=>'13.jpeg'),
		array('url'=>'share_detail.html?id=5','title'=>'草原','img'=>'14.jpeg'),
		array('url'=>'share_detail.html?id=6','title'=>'山水','img'=>'15.jpeg'),
		array('url'=>'share_detail.html?id=7','title'=>'溪流','img'=>'16.jpeg'),
		array('url'=>'share_detail.html?id=8','title'=>'蜜月','img'=>'17.jpeg'),
		array('url'=>'share_detail.html?id=9','title'=>'花草','img'=>'18.jpeg'),
		array('url'=>'share_detail.html?id=10','title'=>'静寂的马路','img'=>'19.jpeg'),
		array('url'=>'share_detail.html?id=11','title'=>'荡舟','img'=>'21.jpeg'),
		array('url'=>'share_detail.html?id=12','title'=>'夜色岛','img'=>'22.jpeg'),
		array('url'=>'share_detail.html?id=13','title'=>'一群美女','img'=>'44.jpeg'),
		array('url'=>'share_detail.html?id=14','title'=>'蓝天白云','img'=>'55.jpeg'),
		array('url'=>'share_detail.html?id=15','title'=>'月亮岛','img'=>'66.jpeg'),
		array('url'=>'share_detail.html?id=16','title'=>'月亮岛','img'=>'77.jpeg'),
		array('url'=>'share_detail.html?id=17','title'=>'月亮岛','img'=>'88.jpeg'),
		array('url'=>'share_detail.html?id=15','title'=>'月亮岛','img'=>'66.jpeg'),
		array('url'=>'share_detail.html?id=16','title'=>'月亮岛','img'=>'77.jpeg'),
		array('url'=>'share_detail.html?id=17','title'=>'月亮岛','img'=>'88.jpeg')

	),
	//首页横幅广告 存放在uploads/ads/下面 大小：800X52
	'banner'=>array('url'=>'http://www.taobao.com/','title'=>'淘宝网','img'=>'ads.png'),
	
);
?>
