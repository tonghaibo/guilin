<?php
/**
 * Y_EMail类
 * @author yuexinok@126.com
 * @version $Id$
 * @copyright 2012-09-03
 * @package default
 * @license http:://weibo.com/yuexinok
 **/
class Y_Mail{
	private static $config;
	private static $socket;
	protected static function init(){
		if(!self::$config){
			self::$config = Y::Config('mail');
		}
		if(!self::$socket){
			self::$socket = @fsockopen(self::$config['smtp'],self::$config['port']);
			if(!self::$socket){
				Y_Debug::addMsg('无法连接smtp服务器!');
				return false;
			}
		}
		return true;
	}
	//发送 $cC抄送邮箱
	//$bCC暗送
	public static function send($to,$from,$subject='',$body='',$cC='',$bB=''){
		if(!self::init()) return false;
		//邮件分割符号
		$boundary = uniqid('');
		$name = iconv('utf-8','GBK',self::$config['name']?self::$config['name']:'');
		//来自邮箱
		$header = "From: ".$name."<".$from.">\r\n";
		//回复来自邮箱
		$header .= "Reply-To: ".$from."\r\n";
		//发给谁支持多人
		if(is_array($to)){
			$to = implode(',',$to);
		}
		$header .= "To: ".$to."\r\n";
		if(is_array($cC)){
			$cC = implode(',',$cC);
			$header .= "Cc: ".$cC."\r\n";
		}
		if(is_array($bB)){
			$header .= "Bcc: ".implode(',',$bB)."\r\n";
		}
		$header .= "Subject: ".iconv('utf-8','GBK',$subject)."\r\n";
		$header .= "Message-ID: <".time().'.'.$from.">\r\n";
		$header .= "Date: ".date('r')."\r\n";
		$header .= "MIME-Version:1.0\r\n";
		$header .= "Content-type:multipart/mixed;boundary=\"".$boundary."\"\r\n\r\n";
		$header .= "--".$boundary."\r\n";
		$mailtype = self::$config['type']?self::$config['type']:'html';
		$charset = self::$config['charset']?self::$config['charset']:'utf-8';
		if($mailtype=='text'){
			$header .= "Content-type:text/plain;charset=\"".$charset."\"\r\n\r\n";
		}
		if($mailtype=='html'){
			$header .= "Content-type:text/html;charset=\"".$charset."\"\r\n\r\n";
		}
		$header .= $body."\r\n\r\n";
		//发送验证
		self::sendCMD('HELO '.self::$config['host']);
		self::sendCMD('AUTH LOGIN '.base64_encode(self::$config['user']));
		self::sendCMD(base64_encode(self::$config['pass']));
		//发送
		self::sendCMD('MAIL FROM:<'.$from.'>');
		self::smtpOK();
		$to = explode(',',$to);
		foreach($to as $val){
			self::sendCMD('RCPT TO:<'.$val.'>');
		}
		self::smtpOK();
		self::sendCMD('DATA');
		self::smtpOK();
		self::sendCMD($header);
		self::smtpOK();
		self::sendCMD(".");
		self::smtpOK();
		if(self::smtpOK()){
			self::sendCMD('QUIT');
			Y_Log::write(array('成功',$subject,join(',',$to)),'mail',0);
			fclose(self::$socket);
			return true;
		}else{
			//状态 主题 发给谁
			Y_Log::write(array('失败',$subject,join(',',$to)),'mail',2);
			return false;
		}
		
	}
		
	//指令发送
	private static function sendCMD($cmd){
		@fputs(self::$socket,$cmd."\r\n");
	}
	//指令是否成功
	private static function smtpOK(){
		$res = str_replace("\r\n",'',@fgets(self::$socket,512));
		if(!preg_match("/^[23]/",$res)){
			self::sendCMD('QUIT');
			@fclose(self::$socket);
			return false;
		}
		return true;
	}
}
?>
