$(function(){
var editor = {
	txt:function(){
		return $('.editor textarea').val();
	},
	delimg:function(obj){
		$(obj).parents('li').remove();
	},
	init:function(){
		$('.editor .close a').click(function(){
			$(this).parents('.editor_menu').hide();
		});
		$('.editor .icon_face').click(function(){
			var offset = $(this).position();
			$('.editor_menu').hide();
			var obj = $('.editor_smiley_icons').parents('.editor_menu');
			obj.css({'left':offset.left});
			obj.show();
			
		});
		//表情
		$('.editor_smiley_icons img').click(function(){
				var t = $('.editor textarea')
				t.val(t.val()+'[emot]'+$(this).attr('title')+'[/emot]');
				$(this).parents('.editor_menu').hide();
		});
		//加粗
		$('.editor .icon_b').click(function(){
			var offset = $(this).position();
			$('.editor_menu').hide();
			var obj = $('.editor_b_icons').parents('.editor_menu');
			obj.css({'left':offset.left});
			obj.show();
			obj.find('.text').val('');
		});
		$('.editor_b_icons button').click(function(){
			var txt = $.trim($('.editor_b_icons input.text').val());
			if(txt!=''){
				var t = $('.editor textarea')
				t.val(t.val()+'[b]'+txt+'[/b]');
				$(this).parents('.editor_menu').hide();
			}
			return false;
			
		});
		//斜体
		$('.editor .icon_i').click(function(){
			var offset = $(this).position();
			$('.editor_menu').hide();
			var obj = $('.editor_i_icons').parents('.editor_menu');
			obj.css({'left':offset.left});
			obj.show();
			obj.find('.text').val('');
		});
		$('.editor_i_icons button').click(function(){
			var txt = $.trim($('.editor_i_icons input.text').val());
			if(txt!=''){
				var t = $('.editor textarea')
				t.val(t.val()+'[i]'+txt+'[/i]');
				$(this).parents('.editor_menu').hide();
			}
			return false;
		});
		//下换线
		$('.editor .icon_u').click(function(){
			var offset = $(this).position();
			$('.editor_menu').hide();
			var obj = $('.editor_u_icons').parents('.editor_menu');
			obj.css({'left':offset.left});
			obj.show();
			obj.find('.text').val('');
		});
		$('.editor_u_icons button').click(function(){
			var txt = $.trim($('.editor_u_icons input.text').val());
			if(txt!=''){
				var t = $('.editor textarea')
				t.val(t.val()+'[u]'+txt+'[/u]');
				$(this).parents('.editor_menu').hide();
			}
			return false;
		});
		//引用
		$('.editor .icon_q').click(function(){
			var offset = $(this).position();
			$('.editor_menu').hide();
			var obj = $('.editor_q_icons').parents('.editor_menu');
			obj.css({'left':offset.left});
			obj.show();
			obj.find('.text').val('');
		});
		$('.editor_q_icons button').click(function(){
			var txt = $.trim($('.editor_q_icons input.text').val());
			if(txt!=''){
				var t = $('.editor textarea')
				t.val(t.val()+'[q]'+txt+'[/q]');
				$(this).parents('.editor_menu').hide();
			}
			return false;
		});
		//换行
		$('.editor .icon_br').click(function(){
			$('.editor textarea').val(editor.txt()+'[br]');
		});
		//url
		$('.editor .icon_a').click(function(){
			var offset = $(this).position();
			$('.editor_menu').hide();
			var obj = $('.editor_a_icons').parents('.editor_menu');
			obj.css({'left':offset.left});
			obj.show();
			obj.find('input.urltitle').val('');
			obj.find('input.url').val('http://');
		});
		$('.editor_a_icons button').click(function(){
			var title = $.trim($('.editor_a_icons input.urltitle').val());
			var url   = $.trim($('.editor_a_icons input.url').val());
			var regx = /^(http|https):\/\/.{1,93}/;
			if(title!='' && regx.test(url)){
				var t = $('.editor textarea')
				t.val(t.val()+'[url href='+url+' ]'+title+'[/url]');
				$(this).parents('.editor_menu').hide();
			}
			return false;
		});
		//图片
		$('.editor .icon_img').click(function(){
			var offset = $(this).position();
			$('.editor_menu').hide();
			var obj = $('.editor_img_icons').parents('.editor_menu');
			obj.css({'left':offset.left});
			obj.show();
			obj.find('.text').val('');
		});
		$('#fileToUpload').change(function(){
			$('#img_msg').hide();
			$('.editor_img_icons input[type=text]').val('');
		});
		$('#buttonUpload').click(function(){
			$(this).siblings('span').hide();
			var txt = $.trim($('#fileToUpload').val());
			var regx = /^.+.(gif|jpeg|bmp|jpg|png)/i;
			if(regx.test(txt)){
				ajaxFileUpload($('.editor_img_icons input[type=text]').val());
			}else{
				$('#img_msg').html('请上传图片格式的文件！').show();
			}
			return false;
		});
	}
};
function ajaxFileUpload(des){
	$("#loadimg")
	.ajaxStart(function(){
		$(this).show();
	})
	.ajaxComplete(function(){
		$(this).hide();
	});
	jQuery.ajaxFileUpload({
		url:'index.php?ro=share&ac=upimg',
		secureuri:false,
		fileElementId:'fileToUpload',
		dataType:'json',
		data:{des:des},
		success: function (data, status){
			if(typeof(data.error) != 'undefined'){
				if(data.error != ''){
					$('#img_msg').html(data.msg).show();
				}else{
					var str = '<li><div><span>';
					str += '<input type="hidden" name="uploads[]" value="'+data.msg.id+'" />';
					str += '<img src="'+data.msg.small+'" title="'+data.msg.des+'" ids="'+data.msg.id+'" onclick="insertimg(this)" />';
					str += '</span></div><a href="javascript::void(0)" onclick="delimg(this)" class="close">关闭</a></li>';
					$('.editor .img_list ul').append(str);
					//$('.editor_img_icons').parents('.editor_menu').hide();
				}
			}
		},
		error: function (data, status, e){
			alert(e);
		}
	});
	return false;
};

editor.init();
})
function delimg(obj){
	if($(obj).parents('li').find('img').attr('ids')==$('#img').val()){
		$('#img').val('');
	}
	$(obj).parents('li').remove();
	
}
function insertimg(obj){
	$('.editor .img_list li').removeClass('sel');
	$(obj).parents('li').addClass('sel');
	$('#img').val($(obj).attr('ids'));
}