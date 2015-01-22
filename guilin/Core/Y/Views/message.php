<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=7">
<title>信息提示页</title>
<style type="text/css">
/*提示页面*/
#error{
margin: 60px auto;
padding: 20px;
width: 580px;
border: 3px solid #F2F2F2;
background: white;
}
#error p{
padding: 0px 0px 0px 40px;
min-height: 40px;
height: auto !important;
height: 40px;
line-height: 40px;
background: url('<?php echo Y_DIR; ?>Views/info.gif') no-repeat;
font-size: 14px;
}
#error p.error{
background: url('<?php echo Y_DIR; ?>Views/error.gif') no-repeat;
}
#error p.ok{
background: url('<?php echo Y_DIR; ?>Views/right.gif') no-repeat;
}
#error p a{color:blue}
#error p a:hover{text-decoration:underline}
#error div{font-size:12px;border-top:1px dotted #CDCDCD;padding-top:5px;}
#error div em{color:red;margin-right:5px;}
</style>
</head>
<body>
<div  style="background:#fff;padding:100px 0px;margin:0 auto;">
<div id='error'>
<p class="<?php echo $type; ?>"><?php echo $mess; ?>&nbsp;&nbsp;<a href='<?php echo $goto_url; ?>'>立即返回</a>
</p>
<div><em id="num"><?php echo $limit_time; ?></em>秒自动跳转</div>
</div>
</div>
<script type="text/javascript">
var seoc = document.getElementById('num');
var time = '<?php echo $limit_time; ?>';
var tt = setInterval(function(){
	time--;
	seoc.innerHTML=time;
	if(time<=0){
		window.location='<?php echo $goto_url;  ?>';
		return;	
	}
},1000);
</script>
</body>
</html>
