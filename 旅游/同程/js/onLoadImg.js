function getOOP(idName) {
    return document.getElementById(idName);
}

function tabBox(thisObj, Num) {
    if (thisObj.className == "at") return;
    var tabObj = thisObj.parentNode.id;
    var tabList = document.getElementById(tabObj).getElementsByTagName("li");
    for (i = 0; i < tabList.length; i++) {
        if (i == Num) {
            thisObj.className = "at";
            document.getElementById(tabObj + "_" + i).style.display = "block";
        } else {
            tabList[i].className = "not";
            document.getElementById(tabObj + "_" + i).style.display = "none";
        }
    }
} 

function delBox(hotelid, id, typeid,cuurepage) {
    getOOP("update").innerHTML = "<span><a onclick=\"UpdateBox(" + hotelid + "," + id + "," + typeid + "," + cuurepage + ");\" style=\"cursor:pointer\" class=\"succ_btn01\">确 定</a></span><span><a id=\"exit1\" href=\"#\" class=\"succ_btn01\">取 消</a></span>";
	squl.create("UpTier", 
	{
		upCol: "delMsg", //弹出控件			
		exitList: "exit0,exit1" //关闭触发器数组用,分割
     });
}

getOOP("btnLi_0").onclick = function () {
    tabBox(getOOP("btnLi_0"), 0);
}

getOOP("btnLi_1").onclick = function () {
    tabBox(getOOP("btnLi_1"), 1);
}

getOOP("btnLi_2").onclick = function () {
    tabBox(getOOP("btnLi_2"), 2);
}

function GetOrderList(picType, currentPage) {
    if (currentPage == null || currentPage == "undefined") {
        currentPage = 1;
    }
    $.ajax({
        type: 'GET',
        url: '/AjaxHandler/DujiaAjaxInterface.aspx?',
        data: 'action=GetPicList&type=' + picType + '&page=' + currentPage + '&date=' + new Date(),
        complete: function (data) {
            if (data.responseText != 'error' && data.responserText != '') {
                if (picType == 0) {
                    $("#infoBox_0").html(data.responseText);
                }
                else if (picType == 1) {
                    $("#infoBox_1").html(data.responseText);
                }
                else if (picType == 2) {
                    $("#infoBox_2").html(data.responseText);
                }
            }
        }
    });
}

function UpdateBox(hotelid, id, typeid, cuurepage) {
    var postData = 'hotelid=' + hotelid + "&id= " + id + "&action=UpdatePic" + "&s=" + Math.random();
    $.ajax({
        type: 'Post',
        url: '/AjaxHandler/DujiaAjaxInterface.aspx',
        data: postData,
        complete: function (data) {
            var msg = data.responseText;
            if (msg == "SUCCESS") {
                getOOP("Messege1").innerHTML = "<h3>操作成功</h3><a onclick=\"GetOrderList(" + typeid + "," + cuurepage + ");closebox();\" style=\"cursor:pointer\"><img src=\"http://img1.40017.cn/cn/new_ui/comprehensive/images/order_center/c_close.gif\"/></a>";
                getOOP("Messege").innerHTML = "<div class=\"txta01\">图片删除成功！</div><div class=\"txta03\"><span><a onclick=\"GetOrderList(" + typeid + "," + cuurepage + ");closebox();\" style=\"cursor:pointer\" class=\"succ_btn01\">确 定</a></span></div>";
                squl.create("UpTier",
                {
                    upCol: "inline_example112" //弹出控件	
                });
            }
            else if (msg == "error") {
                getOOP("ifsecsse").innerHTML = "操作失败";
                getOOP("Messege").innerHTML = "<div class=\"txta01\" >图片删除失败！</div><div class=\"txta03\"><span><a href=\"#\" class=\"succ_btn01\" id=\"exit113\">确 定</a></span></div>";
                squl.create("UpTier",
                {
                    upCol: "inline_example112", //弹出控件			
                    exitList: "exit112,exit113" //关闭触发器数组用,分割
                });
                return;
            }
        }
    });
}

function closebox() {
    try {
        $.colorbox.close();
    } catch (e) { }
}
