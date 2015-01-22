function checkall(name){
var boxes = document.getElementsByName(name);
for(var i=0;i<boxes.length;i++){
	if(boxes[i].checked){
		boxes[i].checked = false;
	}else{
		boxes[i].checked = true;
	}
}
};
function getIds(name){
	var boxes = document.getElementsByName(name);
	var id = '';
	for(var i=0;i<boxes.length;i++){
		if(boxes[i].checked){
			id += boxes[i].value+',';
		}
	}
	return id.substr(0,id.length-1);
};
function link(url){
	window.location.href = url;
};
function checkNum(obj,tips){
	var regix = /^[0-9]+$/;
	var val = $(obj).val();
	var name = $(obj).attr('title');
	if(regix.test(val)){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'格式正确！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'必须为数字0-9！');
		}
		return false;
	}
};
function checkFloat(obj,tips){
	var regix = /^[0-9]+\.?[0-9]+$/;
	var val = $(obj).val();
	var name = $(obj).attr('title');
	if(regix.test(val)){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'格式正确！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'必须为数字数字型！');
		}
		return false;
	}
};
function checkPass(obj,tips){
	var regix = /^\S{6,20}$/;
	var val = $(obj).val();
	var name = $(obj).attr('title');
	if(regix.test(val)){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'格式正确！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'密码长度必须为6-20位');
		}
		return false;
	}
};
function checkW(obj,tips){
	var regix = /^[a-zA-Z-]+$/;
	var val = $(obj).val();
	var name = $(obj).attr('title');
	if(regix.test(val)){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'格式正确！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'必须为字母！');
		}
		return false;
	}
};
function checkStr(obj,tips){
	var regix = /^\w{1,}$/;
	var val = $(obj).val();
	var name = $(obj).attr('title');
	if(regix.test(val)){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'格式正确！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'必须为字母数字下划线！');
		}
		return false;
	}
};
function checkLen(obj,tips,min,max){
	var ln = trim($(obj).val());
	ln = ln.length;
	var name = $(obj).attr('title');
	if(ln<min){
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'字数不能少于'+min+'个！');
		}
		return false;	
	}
	if(ln>max){
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'字数不能大于'+max+'个！');
		}
		return false;	
	}
	if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'字数输入合法！');
		}
	return true;
};
function checkEmail(obj,tips){
	var regix = /^[0-9a-z][_.0-9a-z-]{0,31}@([0-9a-z][0-9a-z-]{0,30}\.){1,4}[a-z]{2,4}$/;
	var val = $(obj).val();
	var name = $(obj).attr('title');
	if(regix.test(val)){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'格式正确！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'输入不合法！');
		}
		return false;
	}
}
function isSelect(obj,tips){
	var val = $(obj).val();
	var name = $(obj).attr('title');
	if(val && val !=0){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'已选择！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'未选择！');
		}
		return false;
	}
};
function isUndefined(variable){
	return typeof variable == 'undefined'?true:false;
};
function trim(str){
	return (str+'').replace(/(\s+)$/g,'').replace(/^\s+/g,'');
};
function checkZh(obj,tips){
	var val = $(obj).val();
	var regix = /^[\u4e00-\u9fa5]{1,}$/;
	var name = $(obj).attr('title');
	if(regix.test(val)){
		if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'输入正确！');
		}
		return true;
	}else{
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'必须为中文！');
		}
		return false;
	}
};
function checkSize(obj,tips,min,max){
	var ln = trim($(obj).val());
	ln = parseInt(ln);
	var name = $(obj).attr('title');
	if(ln<min){
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'的值'+ln+'不能少于'+min+'！');
		}
		return false;	
	}
	if(ln>max){
		if(tips){
			$(tips).removeClass().addClass('error');
			$(tips).html(name+'的值'+ln+'不能大于于'+max+'！');
		}
		return false;	
	}
	if(tips){
			$(tips).removeClass().addClass('ok');
			$(tips).html(name+'值合法！');
		}
	return true;
};
var newtips;
function uptips(str,flag){
	clearTimeout(newtips);
	$('#newstips').removeClass();
	$('#newstips').show();
	if(flag){
		$('#newstips').addClass('ok');
	}
	$('#newstips').html(str);
	newtips = window.setTimeout(function(){
		$('#newstips').hide();
	},2000);
};
function show(obj){
	var img = $(obj).attr('big');
	show_dialog.openHtml('<div style="width:850px;height:512px;padding:5px;" class="bimg"><img style="border-color: #E2EFFF;border-style: solid;border-width: 1px 1px 3px;-moz-box-shadow: 0 0 8px rgba(82, 168, 236, 0.5);-webkit-box-shadow: 0 0 8px rgba(82, 168, 236, 0.5);box-shadow: 0 0 8px rgba(82, 168, 236, 0.5);" src="'+img+'"/></div>','查看大图:'+img,860,550);
}
function showContent(str,title){
	if(title==undefined || title==''){
		title = '查看详情';
	}
	show_dialog.openHtml('<div style="padding:10px;">'+str+'</div>',title,800,300);
}
function show_mess(uid,s){
	var str = '<div style="padding:20px">';
	str += '<h2 style="font-size:18px;color:#666;font-weight:normal;margin-bottom:10px;">给';
	if(s){
		str += '<span style="color:#329ECC;">'+s+'</span>';
	}else{
		str += '<span style="color:#329ECC;">TA</span>';
	}
	str += '写站内私信：</h2>';
	str += '<select id="message_selete" style="width:80px;"><option value="0">常用语</option></select>';
	str += '<input type="hidden" value="'+uid+'" id="message_uid" />';
	str += '<input type="hidden" value="'+uid+'" />';
	str += '<textarea id="message_text" style="width:480px;height:150px;color:#666;line-height:20px;margin-bottom:10px;padding:4px;border: 1px solid #aebacd;font-size: 14px;"></textarea>';
	str += '<button class="button8" onclick="send_mess()">提交</button><span style="padding-left:10px;" id="message_error"></span> <a style="margin-left:5px;" href="govern.php?ro=sys&ac=notice&method=add&uid='+uid+'">或者给TA写通知</a>';
	str += '</div>';
	show_dialog.open(str,540,200);
	$.post('?ro=member&ac=mess&method=getlist',{},function(d){
		if(d!=''){
			$('#message_selete').append(d);
		}
	});
	$('#message_selete').change(function(){
		if($(this).val()!=0){
			$('#message_text').val($(this).val());
		}
	});
}
function send_mess(){
	var mess = $('#message_text').val();
	var uid = $('#message_uid').val();
	$.post('?ro=member&ac=mess&method=add',{'uid':uid,'mess':mess},function(d){
		if(d==0){
			$('#message_error').css({color:'green'}).html('发送成功！');
		}else{
			$('#message_error').css({color:'red'}).html('发送失败！');
		}
	});
	setTimeout(show_dialog.close(),3000);
	
}
function linkuser(uid){
	var url = '?ro=member&ac=member&method=view&uid='+uid;
	show_dialog.openUrl(url,800,450,'UID：'+uid);
}
function showorder(id,t){
var url = '?ro=order&method=view&id='+id+'&ac='+t;
	show_dialog.openUrl(url,800,400,'订单号：'+id);
}
function show_wayline(id){
	var url = '?ro=mod&ac=wayline&method=view&id='+id;
	show_dialog.openUrl(url,850,600,'线路ID：'+id,true);
}
function show_hotel(id){
	var url = '?ro=mod&ac=hotel&method=view&id='+id;
	show_dialog.openUrl(url,850,600,'酒店ID：'+id,true);
}
function show_content(id){
	var url = '?ro=mod&ac=content&method=view&id='+id;
	show_dialog.openUrl(url,850,600,'文章ID：'+id,true);
}
function show_ip(ip){
if(ip){
	$.get('govern.php?ro=index&ac=ip&ip='+ip,{},function(d){
	if(d!=''){
		var str = '';
		d = eval('('+d+')');
		if(d.code==0){
			str = d.data.country+''+d.data.area+''+d.data.region+''+d.data.city+' 运营商：'+d.data.isp;
		}else{
			str = '解析出错！代号：'+d.code;
		}
		show_msg.open(str,3,3);
		return;
	}
	});
}
}