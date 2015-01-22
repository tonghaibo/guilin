function trim(str){
	return (str+'').replace(/(\s+)$/g,'').replace(/^\s+/g,'');
}
//获取光标位置
function setFocusLast(id){//obj是个文本框对象  
    var obj  = document.getElementById(id);
    if (obj.setSelectionRange) {   
        setTimeout(function(){  
            obj.setSelectionRange(obj.value.length, obj.value.length);    
            obj.focus()
            } ,0)   
    }else if (obj.createTextRange) {  
        var textrange=obj.createTextRange();  
        textrange.moveStart("character",obj.value.length);  
        textrange.moveEnd("character",0);  
        textrange.select();  
    }    
}
//on focus=>type=0,on change=>type=1
function changeVCode(type,imgId){
    if(typeof(imgId)=='undefined'){
        imgId = 'validatecode';
    }
    var imgObj = $('#'+imgId);
    var isF =imgObj.attr('iff');
    if((type==0 && isF!='1') || type==1){
        imgObj.attr('src','index_vcode.html?r=' + Math.round((Math.random()) * 100000000));
        $('#showCode').show();
        imgObj.show();
        if(isF!='1'){
            imgObj.attr('iff',"1");
        }
    }
}
//鼠标放入input
function getValidateCode(inputid,imgId){
    if($('#'+imgId).attr('src')==''){
        $('#validateBox').show();
        changeVCode(1);
    }
}
//清空并隐藏验证码
function resetValidateCode(imgId){
    if(!imgId){
        imgId = 'validatecode';
    }
    $('#validateBox').hide();
    $('#'+imgId).attr('src', '');
}
//初始化数据
function initData(){
    $('#r_uid').val($('#c_uid').val());
    $('#text_comment').val('');
    $('#verify').val('');
	$('#r_id').val(0);
}
//点击回复赋值
function reply(nick_name,recomID,replyID) {
    $('#text_comment').val('回复@'+nick_name+'：');
    $('#text_comment').focus();
	setFocusLast('text_comment');
    $('#r_id').val(recomID);
    $('#r_uid').val(replyID);
}
//获取评论列表
function loadCommentList(url,b){ 
    $('#Pagination_comment').load(url,{
    },function(){
        if(b){
            goCommentArea();
        }
    });
	//获取评论列表
}
function goCommentArea(){
    var mtrTop=$('#Pagination_comment').offset().top-65;
    $("html,body").scrollTop(mtrTop);
}
//新版评论定位
function goComment(){
    var mtrTop=$('#AddComment').offset().top-5;
    $("html,body").scrollTop(mtrTop);
}
function show_feedback(){
	var url = window.location.href;
	var str = '<div style="padding:10px 20px;">';
	str += '<h2 style="font-size:18px;color:#666;font-weight:normal;margin-bottom:10px;">我们真诚的聆听您的声音</h2>';
	str += '<textarea id="feedback_text" style="width:480px;height:150px;color:#666;line-height:20px;margin-bottom:10px;padding:4px;border: 1px solid #aebacd;font-size: 14px;">';
	str += '欢迎提出宝贵的意见和建议。抱歉我们无法逐一回复，但我们会认真阅读，你的支持是对我们最大的鼓励和帮助。</textarea>';
	str += '<button class="button8" onclick="send_feedback()">提交</button><span id="feedback-errinfo" style="display:none;color:red;padding-left:10px;">内容不能为空</span></div>';
	show_dialog.open(str,540,245);
	$("#feedback_text").focus(function() {
    if($(this).val() == "欢迎提出宝贵的意见和建议。抱歉我们无法逐一回复，但我们会认真阅读，你的支持是对我们最大的鼓励和帮助。")
    $(this).val("");
    }).blur(function() {
    if($(this).val() == "")
    $(this).val("欢迎提出宝贵的意见和建议。抱歉我们无法逐一回复，但我们会认真阅读，你的支持是对我们最大的鼓励和帮助。");
});
}
//加关注
function add_attention(obj){
	if(obj){
		var uid = $(obj).attr('uid');
		if(uid){
			$.post('user_addattention.html',{uid:uid},function(data){
				switch(data){
					case '1':
						show_msg.open('TA已经是您的好友了！',3,3);
						break;
					case '2':
						show_msg.open('您的好友数，已经超出限定值！',3,3);
						break;
					case '3':
						show_msg.open('TA的好友数，已经超出限定值',3,3);
						break;
					case '4':
						show_msg.open('添加关注成功！',3,1);
						$(obj).html('已关注').attr({'onClick':''});
						break;
					case '5':
						show_msg.open('请先登录！',3,2);
						break;
					case '6':
						show_msg.open('您不能关注您自己！',3,3);
						break;
					default:
						show_msg.open('关注失败！',3,2);
						break;
					
				}
			});
		}
	}
}
function del_attention(obj){
	if(obj){
		var uid = $(obj).attr('uid');
		if(uid){
			$.post('user_delattention.html',{uid:uid},function(data){
				if(data=='1'){
					show_msg.open('请先登录！',3,2);
					return;
				}
				if(data=='2'){
					show_msg.open('取消关注成功！',3,1);
					$(obj).html('已取消').attr({'onclick':''});
					return;
				}
				show_msg.open('取消关注失败!',3,2);
			});
		}
	}
}
//打开
function send_msg(obj){
if(obj){
	var str = '<div style="padding:10px 20px;">';
	str += '<h2 style="font-size:18px;color:#666;font-weight:normal;margin-bottom:10px;">给<span class="c369">'+$(obj).attr('title')+'</span>写私信</h2>';
	str += '<input type="hidden" value="'+$(obj).attr('uid')+'" id="msg_fuid" />';
	str += '<textarea id="msg_text" onfocus="$(\'#msg_errinfo\').hide();" style="width:430px;height:100px;color:#666;line-height:20px;margin-bottom:10px;padding:4px;border: 1px solid #aebacd;font-size: 14px;"></textarea>';
	str += '<button class="button8" onclick="send_msgs()">发送</button><span id="msg_errinfo" style="display:none;color:red;padding-left:10px;">内容不能为空</span></div>';
	show_dialog.open(str,490,195);
}
}
//发送
function send_msgs(){
	var content = trim($('#msg_text').val());
	var uid = trim($('#msg_fuid').val());
	if(content==''){
		$('#msg_errinfo').show().html('信件内容不能为空！');
	}else if(content.length>140){
		$('#msg_errinfo').show().html('信件内容不能超过140个字！');
	}
	if(uid==''){
		$('#msg_errinfo').show().html('您发送的对象不存在！');
		show_dialog.close();
	}
	$.post('user_sendmsg.html',{uid:uid,content:content},function(data){
		var o = $('#msg_errinfo');
		switch(data){
			case '1':
				o.show().html('请先登录！<a href="index_login.html">立即登陆</a>');
				break;
			case '2':
				o.show().html('内容不能为空！');
				break;
			case '4':
				o.show().html('您不能给自己发送信件！');
				break;
			case '6':
				o.show().html('您当日发送的信件已经超出！');
				break;
			case '5':
				show_msg.open('信件发送成功!',3,1,show_dialog.close());
				break;
			case '7':
				o.show().html('对方设置,只有TA的好友才能给TA发送信息！');
				break;
			case '8':
				o.show().html('对方设置,只有TA的好友和粉丝才能给TA发送信息！');
				break;
			default:
				o.show().html('未知原因！发送失败！');
		}
	});
}
//加收藏
function addfavor(id,t){
	if(id && t){
		$.post('user_addfavor.html',{id:id,t:t},function(data){
			switch(data){
				case '0':
					show_msg.open('添加收藏成功！',3,1);
					break;
				case '1':
					show_msg.open('添加收藏失败！',3,2);
					break;
				case '2':
					show_msg.open('请先登录！',3,2);
					break;
				case '3':
					show_msg.open('您已经添加收藏了',3,3);
					break;
				case '4':
					show_msg.open('您的收藏数已经达到上限',3,3);
					break;
				default:
					show_msg.open('未知原因失败！',3,2);
					break;
			}
		});
	}
}
function send_feedback(){
	var content = trim($("#feedback_text").val());
	var url = window.location.href;
    if(content!="" && content!="欢迎提出宝贵的意见和建议。抱歉我们无法逐一回复，但我们会认真阅读，你的支持是对我们最大的鼓励和帮助。") {
        $.post("index_feedback.html", {content:content,url:url}, function(d) {
            if(d== 0) {
                $("#feedback_text").fadeOut(250, function() {
                    $(this).html("<div style='text-align:center;font-size:24px;color:#582e09;padding:20px 0;font-weight:normal'>提交成功，谢谢！</div>").fadeIn(250, function() {
                        setTimeout(show_dialog.close(), 3000);
                    });
                });
            } else {
                $("#feedback-errinfo").show();
            }
        });
    } else {
        $("#feedback-errinfo").show();
        $("#feadback_text").val("");
    }
};
function show_map(id){
show_dialog.openUrl('map_loadhtml.html?hid='+id,650,450,'查看地图');
}
function show_content(str,title){
show_dialog.openHtml('<div style="padding:10px;font-size:13px;color:#444;line-height:18px;">'+str+'</div>',title,550,350);
}
$(function(){
$('#SearchText').keyup(function(){
	if($(this).val()!='' && $(this).val()!=$(this).attr('data_tip')){
		$('#SearchTips .SearchTipsList li span').html($(this).val());
		$('#SearchTips').show();
	}
}).blur(function(){
	if($(this).val()==''){
		$(this).val($(this).attr('data_tip'));
	}
}).focus(function(){
	if($(this).val()==$(this).attr('data_tip')){
		$(this).val('');
	}
});
$('#SearchTips li').hover(function(){
	$(this).addClass('Stlhover').siblings().removeClass('Stlhover');
}).click(function(){
	var t = $(this).attr('id');
	var keyword = $.trim($('#SearchText').val());
	if(keyword!=''){
		if(t=='map'){
			window.location.href="map.html?sText="+keyword;
		}else if(t=='wayline'){
			window.location.href="wayline.html?sText="+keyword;
		}else if(t=='share'){
			window.location.href="share.html?sText="+keyword;
		}else{
			window.location.href="search_"+t+".html?sText="+keyword;
		}
	}
});
$('body').not('div.SearchBox').click(function(){
	$('#SearchTips .SearchTipsList li span').html('');
	$('#SearchTips').hide();
});
$('#pl_part_goTop').click(function(){
	$('html,body').animate({scrollTop:0},200);
});
var backToTopFun = function(){
var st = $(document).scrollTop(),winh=$(window).height();
(st>0)?$('#pl_part_goTop').show():$('#pl_part_goTop').hide();
//IE6
if(!window.XMLHttpRequest){
	$('#pl_part_goTop').css('top',st+winh-166);
}
};
$(window).bind('scroll',backToTopFun);
backToTopFun();
});