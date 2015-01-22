<?php
/**
 * Y框架 时间处理
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-08-24
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
if(!defined('IN_Y')) die;
class Y_Date {
    public function isLeapYear($year='') {
        if(empty($year)) {
            $year = $this->year;
        }
        return ((($year % 4) == 0) && (($year % 100) != 0) || (($year % 400) == 0));
    }

    /**
     +----------------------------------------------------------
     * 计算日期差
     *
     *  w - weeks
     *  d - days
     *  h - hours
     *  m - minutes
     *  s - seconds
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param mixed $date 要比较的日期
     * @param string $elaps  比较跨度
     +----------------------------------------------------------
     * @return integer
     +----------------------------------------------------------
     */
    public static function dateDiff($date,$elaps = "d") {
        $__DAYS_PER_WEEK__       = (7);
        $__DAYS_PER_MONTH__      = (30);
        $__DAYS_PER_YEAR__       = (365);
        $__HOURS_IN_A_DAY__      = (24);
        $__MINUTES_IN_A_DAY__    = (1440);
        $__SECONDS_IN_A_DAY__    = (86400);
        //计算天数差
        $__DAYSELAPS = (time()-$date) / $__SECONDS_IN_A_DAY__ ;
        switch ($elaps) {
            case "y"://转换成年
                $__DAYSELAPS =  $__DAYSELAPS / $__DAYS_PER_YEAR__;
                break;
            case "M"://转换成月
                $__DAYSELAPS =  $__DAYSELAPS / $__DAYS_PER_MONTH__;
                break;
            case "w"://转换成星期
                $__DAYSELAPS =  $__DAYSELAPS / $__DAYS_PER_WEEK__;
                break;
            case "h"://转换成小时
                $__DAYSELAPS =  $__DAYSELAPS * $__HOURS_IN_A_DAY__;
                break;
            case "m"://转换成分钟
                $__DAYSELAPS =  $__DAYSELAPS * $__MINUTES_IN_A_DAY__;
                break;
            case "s"://转换成秒
                $__DAYSELAPS =  $__DAYSELAPS * $__SECONDS_IN_A_DAY__;
                break;
        }
        return $__DAYSELAPS;
    }

    /**
     +----------------------------------------------------------
     * 人性化的计算日期差
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param mixed $time 要比较的时间
     * @param mixed $precision 返回的精度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public static function timeDiff($time,$precision=false) {
        if(!is_numeric($precision) && !is_bool($precision)) {
            static $_diff = array('y'=>'年','M'=>'个月','d'=>'天','w'=>'周','s'=>'秒','h'=>'小时','m'=>'分钟');
            return ceil(self::dateDiff($time,$precision)).$_diff[$precision].'前';
        }
        $diff = abs($time -time());
        static $chunks = array(array(31536000,'年'),array(2592000,'个月'),array(604800,'周'),array(86400,'天'),array(3600 ,'小时'),array(60,'分钟'),array(1,'秒'));
        $count =0;
        $since = '';
        for($i=0;$i<count($chunks);$i++) {
            if($diff>=$chunks[$i][0]) {
                $num   =  floor($diff/$chunks[$i][0]);
                $since .= sprintf('%d'.$chunks[$i][1],$num);
                $diff =  (int)($diff-$chunks[$i][0]*$num);
                $count++;
                if(!$precision || $count>=$precision) {
                    break;
                }
            }
       }
        return $since.'前';
    }
    /**
     +----------------------------------------------------------
     * 日期数字转中文
     * 用于日和月、周
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param integer $number 日期数字
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public static function  numberToCh($number) {
        $number = intval($number);
        $array  = array('一','二','三','四','五','六','七','八','九','十');
        $str = '';
        if($number  ==0)  { $str .= "十" ;}
        if($number  <  10){
           $str .= $array[$number-1] ;
        }
        elseif($number  <  20  ){
           $str .= "十".$array[$number-11];
        }
        elseif($number  <  30  ){
           $str .= "二十".$array[$number-21];
        }
        else{
           $str .= "三十".$array[$number-31];
        }
        return $str;
    }

    /**
     +----------------------------------------------------------
     * 年份数字转中文
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param integer $yearStr 年份数字
     * @param boolean $flag 是否显示公元
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public static function  yearToCh( $yearStr ,$flag=false ) {
        $array = array('零','一','二','三','四','五','六','七','八','九');
        $str = $flag? '公元' : '';
        for($i=0;$i<4;$i++){
            $str .= $array[substr($yearStr,$i,1)];
        }
        return $str;
    }

    /**
     +----------------------------------------------------------
     *  判断日期 所属 干支 生肖 星座
     *  type 参数：XZ 星座 GZ 干支 SX 生肖
     *
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $type  获取信息类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public static function magicInfo($time,$type) {
	    $result = '';
	    $m      =   date('m',$time);
	    $y      =   date('Y',$time);
	    $d      =   date('d',$time);
	    switch ($type) {
        	case 'XZ'://星座
            		$XZDict = array('摩羯','宝瓶','双鱼','白羊','金牛','双子','巨蟹','狮子','处女','天秤','天蝎','射手');
            		$Zone   = array(1222,122,222,321,421,522,622,722,822,922,1022,1122,1222);
            		if((100*$m+$d)>=$Zone[0]||(100*$m+$d)<$Zone[1])
                		$i=0;
            		else
                		for($i=1;$i<12;$i++){
                			if((100*$m+$d)>=$Zone[$i]&&(100*$m+$d)<$Zone[$i+1])
                  			break;
                		}
            		$result = $XZDict[$i].'座';
            		break;
        	case 'GZ'://干支
            		$GZDict = array(array('甲','乙','丙','丁','戊','己','庚','辛','壬','癸'),array('子','丑','寅','卯','辰','巳','午','未','申','酉','戌','亥'));
            		$i= $y -1900+36 ;
            		$result = $GZDict[0][$i%10].$GZDict[1][$i%12];
            		break;
        	case 'SX'://生肖
            		$SXDict = array('鼠','牛','虎','兔','龙','蛇','马','羊','猴','鸡','狗','猪');
            		$result = $SXDict[($y-4)%12];
            		break;
        }
        return $result;
    }
}
