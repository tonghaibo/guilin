/***********************************************************
    [EasyTalk] (C)2009 - 2011 nextsns.com
    This is NOT a freeware, use is subject to license terms

    @Filename ye_dialog.js $

    @Author hjoeson $

    @Date 2011-01-09 08:45:20 $
*************************************************************/
var bool=false;
var show_dialog = {
    Counter: 0,
    init: function(){
        if($.browser.msie && $.browser.version=='6.0'){$("select").css("visibility", "hidden");}
		var a='<div id="ye_dialog_overlay"></div>';
		a+='<div class="wblayerbox" id="ye_dialog_window">';
        a+='<table class="wbLayer">';
        a+='    <tr><td class="topl"></td><td class="topm"></td><td class="topr"></td></tr>';
        a+='    <tr><td height="100%" ><div class="ml"></div></td><td class="mm" valign="top">';
        a+='        <a id="ye_dialog_close"></a><div id="ye_dialog_title"></div><div id="ye_dialog_body"></div>';
        a+='    </td><td height="100%"><div class="mr"></div></td></tr>';
        a+='    <tr><td class="bottoml" height="5px"></td><td class="bottomm"></td><td class="bottomr"></td></tr>';
        a+='</table>';
        a+='</div>';
		$("body").append(a);
        $("#ye_dialog_close").click(function(){
            show_dialog.close();
            return false;
        });
		$("#ye_dialog_title").mousedown(function(event) {
            bool = true;
            offsetX = event.offsetX ? event.offsetX : event.layerX;
            offsetY = event.offsetY ? event.offsetY : event.layerY;
        }).mouseup(function() {
            bool = false;
        })
        $(document).mousemove(function(event) {
            if (!bool) {
                return;
            } else {
                var x = event.pageX - offsetX;
                var y = event.pageY - offsetY;
                $("#ye_dialog_window").css("position", "absolute");
                $("#ye_dialog_window").css({"left":x,'top':y,'margin':0});
            }
        })
        $("#ye_dialog_overlay").show();
        this.position();
        return this;
    },
    openHtml: function(b, e, a, d, c, f){/*(b:html,e:title,a:width,d:height,c:callback,f:closeFun)*/
        if ($.isFunction(f)) {
            this.closeFun = f;
        }
        this.init();
        $("#ye_dialog_body").html(b)
        this.title(e == undefined ? "" : e);
        this.resize(a ? a : 300, d ? d : 150,true);
        $("#ye_dialog_window").show();
        if ($.isFunction(c)) {
            c();
        }
        return this;
    },
	open: function(b,a, d, c, f){/*(b:html,e:title,a:width,d:height,c:callback,f:closeFun)*/
        if ($.isFunction(f)) {
            this.closeFun = f;
        }
        this.init();
        $("#ye_dialog_body").html(b)
        //this.title(e == undefined ? "" : e);
		$('#ye_dialog_title').hide();
        this.resize(a ? a : 300, d ? d : 150);
        $("#ye_dialog_window").show();
        if ($.isFunction(c)) {
            c();
        }
        return this;
    },
    openUrl: function(c, a, e, g, scrolling){/*(c:url,a:width,e:height,g:title,f:scroll)*/
        this.init();
        var b = a != undefined ? a : 300;
        var f = e != undefined ? e : 150;
		var s = scrolling != undefined ? scrolling : 'no';
        var d = (new Date).getTime();
        if (c.indexOf("?") == -1) {
            c = c + "?_t=" + d;
        } else {
            c = c + "&_t=" + d;
        }
        this.title(g == undefined ? "\u540C\u5B66" : g);
        $("#ye_dialog_body").html('<iframe width="1px" height="2px" id="ye_dialog_iframe" scrolling="'+s+'" frameborder="0"></iframe>');
        $("#ye_dialog_iframe").attr("src", c);
        this.resize(b, f,true);
        $("#ye_dialog_window").show()
    },
    close: function(){
    	var b = this.closeFun ? this.closeFun : function(){};
		b();
        $("#ye_dialog_window").remove();
        $("#ye_dialog_overlay").remove();
        if($.browser.msie && $.browser.version=='6.0'){$("select").css("visibility", "visible");}
        return this
    },
    resize: function(a, c,f){//(a:width---int, b:height----int)
        var d = a ? a : 300;
        var b = c ? c : 150;
        $("#ye_dialog_window").css({
            width: d + "px",
            height: b + "px"
        });
		if(f){
			$("#ye_dialog_body").css({
				width: d + "px",
				height: (b-28) + "px"
        });
		}
		//if (!$.browser.msie) {
			//$("#ye_dialog_body").css("height", "99%").css("height", c - 28 + "px");
		//}
        this.position();
        return this
    },
    position: function(){
        var b = $("#ye_dialog_window").width();
        var a = $("#ye_dialog_window").height();
        $("#ye_dialog_window").css({
            marginLeft: "-" + parseInt(b / 2) + "px"
        });
        if (!($.browser.msie && $.browser.version < 7)) {
            $("#ye_dialog_window").css({
                marginTop: "-" + parseInt(a / 2) + "px"
            })
        }
        return this
    },
    title: function(a){
        if (a != undefined) {
            $("#ye_dialog_title").text(a);
            return this
        } else {
            return $("#ye_dialog_title").text()
        }
    }
};
// ico 1正确，2错误，3提示，4警告，5疑问
var show_msg = {
    Counter: 0,
    open: function(e, d, c, b){//(e:html,d:timer,c:type(int),b:callback) 秒数
        $('.ye_msg_window').remove();
        var jstz = e.indexOf('<script');
        if (jstz!=-1) {
            e='正在载入中...'+e;
        }
        this.Counter++;
		var len = e.length*14+80;
		var a='<div id="ye_msg_' + this.Counter + '" class="wblayerbox ye_msg_window">';
        a+='<table class="wbLayer" style="width:'+len+'px;height:58px">';
        a+='    <tr><td class="topl"></td><td class="topm"></td><td class="topr"></td></tr>';
        a+='    <tr><td class="ml"></td><td class="mm" valign="top">';
        a+='        <div class="ye_msg_wrap">' + e + '</div>';
        a+='    </td><td class="mr"></td></tr>';
        a+='    <tr><td class="bottoml"></td><td class="bottomm"></td><td class="bottomr"></td></tr>';
        a+='</table>';
        a+='</div>';

        $("body").append(a);
        $("#ye_msg_" + this.Counter).find('.ye_msg_wrap').addClass("ye_msg_ico_" + c);
        this.position(this.Counter);
        if (typeof b == "function") {
            b();
        }
        if (d != undefined && d != 0) {
            setTimeout('$("#ye_msg_' + this.Counter + '").remove();', d * 1000)
        }
    },
    position: function(c){//(c:this.Counter)
        var b = $("#ye_msg_" + c).width();
        var a = $("#ye_msg_" + c).height();
        $("#ye_msg_" + c).css({
            marginLeft: "-" + parseInt(b / 2 + 5) + "px"
        })
    }
};
var show_comfirm = {
    open: function(e,h,f){//(e:offset,h:html,f:callback)
        $('.ye_msg_window').remove();

		var a='<div class="wblayerbox ye_msg_window ye_msg_comfirm" style="height:0px">';
        a+='<table class="wbLayer">';
        a+='    <tr><td class="topl"></td><td class="topm"></td><td class="topr"></td></tr>';
        a+='    <tr><td class="ml"></td><td class="mm" valign="top">';
        a+='        <div class="ye_msg_wrap ye_msg_comfirm_body"><p>' + h + '</p><input type="button" class="button8" value="确定">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button9" value="取消"></div>';
        a+='    </td><td class="mr"></td></tr>';
        a+='    <tr><td class="bottoml"></td><td class="bottomm"></td><td class="bottomr"></td></tr>';
        a+='</table>';
        a+='</div>';

        $("body").append(a);
		this.position(e);
		
		$('.ye_msg_comfirm').animate({height: $('.ye_msg_comfirm > .wbLayer').css('height')}, "fast");
		
		$('.ye_msg_comfirm_body').delegate('.button8','click',function(){
			$('.ye_msg_comfirm').animate({height: '0px'}, "fast",'',function(){
				$('.ye_msg_comfirm').remove();
			});
			if (typeof f == "function") {
				f();
			}
		});
		$('.ye_msg_comfirm_body').delegate('.button9','click',function(){
			$('.ye_msg_comfirm').animate({height: '0px'}, "fast",'',function(){
				$('.ye_msg_comfirm').remove();
			});
		});
    },
    position: function(e){
        $('.ye_msg_comfirm').css({
            top: (e.top-parseInt($('.ye_msg_comfirm > .wbLayer').css('height'))) + "px",
			left: (e.left-95) + "px"
        })
    }
};