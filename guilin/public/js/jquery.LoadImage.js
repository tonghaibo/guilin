/*
**************图片预加载插件******************
///作者：没剑(2008-06-23)
///http://regedit.cnblogs.com
///修改时间：2011-12-16

///说明：在图片加载前显示一个加载标志，当图片下载完毕后显示图片出来
可对图片进行是否自动缩放功能
此插件使用时可让页面先加载，而图片后加载的方式，
解决了平时使用时要在图片显示出来后才能进行缩放时撑大布局的问题
///参数设置：
scaling     是否等比例自动缩放
width       图片最大高
height      图片最大宽
loadpic     加载中的图片路径
*/
jQuery.fn.LoadImage = function(scaling, width, height, loadpic) {
    if (loadpic == null) loadpic = "/Admin/Common/Images/nopic.gif";
    return this.each(function() {
        var t = $(this);
        var src = t.attr("src");
        if (src == null || src == '') {
            src = loadpic;
            t.attr("src", loadpic);
        }
        t.width(width);
        t.height(height);
        var img = new Image();
        img.src = src;
        //自动缩放图片
        var autoScaling = function() {
            if (scaling) {
                if (img.width > 0 && img.height > 0) {
                    if (img.width / img.height >= width / height) {
                        if (img.width > width) {
                            t.width(width);
                            t.height((img.height * width) / img.width);
                        } else {
                            t.width(img.width);
                            t.height(img.height);
                        }
                    }
                    else {
                        if (img.height > height) {
                            t.height(height);
                            t.width((img.width * height) / img.height);
                        } else {
                            t.width(img.width);
                            t.height(img.height);
                        }
                    }
                }
            }
        }
        //处理ff下会自动读取缓存图片
        if (img.complete) {
            autoScaling();
            return;
        }
        t.attr("src", loadpic);
        //var loading = $("<img alt=\"加载中\" title=\"图片加载中\" src=\"" + loadpic + "\" />");        
        //t.hide();
        //t.after(loading);
        $(img).load(function() {
            autoScaling();
            //loading.remove();
            t.attr("src", this.src);
            //t.show();
            //alert("finally!")
        });
    });
}

/*附加方法*/
function eq(i) {
    $(function() {
        $(".nav .nav_title ul li").eq(i - 1).attr("class", "hover");
    })
}
$(function() {
    try {
        $("#imgSubmit").click(function() {
            var tit = $.trim($("#txt_search").val());
            if (tit == "") {
                alert('搜索不能为空!');
                return false;
            } else {
                top.location.href = '/templet/search.aspx?tit=' + escape(tit);
            }
        })
    } catch (err) { }
})