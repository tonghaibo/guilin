/*
 *   2012-12-29
 *   现金券修改
 */
var orderTypeResult = 0;
var timer;
function getOOP(idName) {                   		// 获取对象
    return document.getElementById(idName);
}

function getChildList(parentId, childName) {		//根据ID获得子项列表
    return document.getElementById(parentId).getElementsByTagName(childName);
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
var liList = getChildList("LiList", "li");
for (i = 0; i < liList.length; i++) {
	if(liList[i].className != "menu_at"){
		liList[i].onmouseover = function (){this.className='at';};
		liList[i].onmouseout = function (){this.className=''};
	}
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
    if (Num == 2) {
        getOOP("conditionSearch").style.display = "none";
    }
    else {
        getOOP("conditionSearch").style.display = "block";
    }
    GetOrderList(Num, 1);
}
window.onload = function () {
    GetOrderList(0, 1);
}
function setIsSearch() {
    document.getElementById("setIsSearch").value = "1";
    if (orderTypeResult == 1) {
        GetOrderList(0, 1);
    }
    else if (orderTypeResult == 2) {
        GetOrderList(1, 1);
    }
//    else {
//        GetOrderList(1, 2);
//    }
    
}
var ajacInfo;
function GetOrderList(orderType, currentPage) {
	//切换的时候把弹出层隐藏掉
	squl.dom("#lineChange")[0].style.display="none";
	squl.dom("#clReason")[0].style.display="none";
	
    //等待框 start
    if (orderType == 0) {
        $("#infoBox_0").html("<table border='0' cellspacing='1' cellpadding='1'><tr><th width='114'>订单号</th><th width='172'>航班信息</th><th width='127'>起飞时间</th><th width='60'>价格</th><th width='86'>订单状态</th><th width='218'>订单操作</th></tr></table><div class='info_end'><img src='http://img1.40017.cn/cn/comm/images/cn/public/round_loading37.gif'/>&nbsp;&nbsp;<span style='line-height:36px;overflow:hidden;display:inline-block;'>订单载入中...</span></div>");
        orderTypeResult = 1;
    }
    else if (orderType == 1) {
        $("#infoBox_1").html("<table border='0' cellspacing='1' cellpadding='1'><tr><th width='114'>订单号</th><th width='172'>航班信息</th><th width='127'>起飞时间</th><th width='60'>价格</th><th width='86'>订单状态</th><th width='218'>订单操作</th></tr></table><div class='info_end'><img src='http://img1.40017.cn/cn/comm/images/cn/public/round_loading37.gif'/>&nbsp;&nbsp;<span style='line-height:36px;overflow:hidden;display:inline-block;'>订单载入中...</span></div>");
        orderTypeResult = 2;
    }
    else if (orderType == 2) {
        $("#infoBox_2").html("<table border='0' cellspacing='1' cellpadding='1'><tr><th width='114'>订单号</th><th width='172'>航班信息</th><th width='127'>起飞时间</th><th width='60'>价格</th><th width='86'>订单状态</th><th width='218'>订单操作</th></tr></table><div class='info_end'><img src='http://img1.40017.cn/cn/comm/images/cn/public/round_loading37.gif'/>&nbsp;&nbsp;<span style='line-height:36px;overflow:hidden;display:inline-block;'>订单载入中...</span></div>");
        orderTypeResult = 3;
    }
    //等待框 end
    var setIsSearch = document.getElementById("setIsSearch").value;
    //alert(setIsSearch);

    //判断是进行中还是已结束 start
    var ingCap = document.getElementById("btnLi_0");
    var endCap = document.getElementById("btnLi_1");
    var interCap = document.getElementById("btnLi_2");
    if (ingCap.className == "at") {
        orderType = 0;
    }
    else if (endCap.className == "at") {
        orderType = 1;
    }
    else if (interCap.className == "at") {
        orderType = 2;
    }
    //判断是进行中还是已结束 end 

    var pdstarttime = document.getElementById("txtstartTime").value;
    var pdendtime = document.getElementById("txtendTime").value; 
    var Pasepdstarttime = new Date(Date.parse(pdstarttime.replace(/-/g, "/")));
    var Pasepdendtime = new Date(Date.parse(pdendtime.replace(/-/g, "/")));     
    if (Pasepdstarttime > Pasepdendtime) 
    {
        alert("结束时间不能大于开始时间！");
        document.getElementById("txtendTime").click();
        return ;
    }

    if (currentPage == null || currentPage == "undefined") {
        currentPage = 1;
    }
    var passengerName="";
    var starttime="";
    var endtime="";
    var SearchType = "";
    var urlValue = "";
    var fTypeValue = "";
    if (orderType == 0 || orderType == 1) {
        urlValue = "/AjaxHandler/FlightAjaxInterface.aspx";
        fTypeValue = "GetFlightOrderList";
    }
    else {
        urlValue = "/AjaxHandler/FlightInterAjaxInterface.aspx";
        fTypeValue = "GetFlightInterOrderList";
    }

    passengerName = document.getElementById("txtpassengerName").value;
    starttime = document.getElementById("txtstartTime").value;
    endtime = document.getElementById("txtendTime").value; 
    SearchType = document.getElementById("ddlType").value;
    if(ajacInfo){    	
    	ajacInfo.abort();
    }
    ajacInfo = $.ajax({
        type: 'GET',
        url: urlValue,
        //data: 'fType=GetFlightOrderList&orderType=' + orderType + '&currentPage=' + currentPage + '&passengerName=' + passengerName + '&starttime=' + starttime + '&endtime=' + endtime + '&SearchType=' + SearchType + '&date=' + new Date(),
        data:{
            fType: fTypeValue,
            orderType:orderType,
            currentPage: currentPage,
            passengerName:passengerName,
            starttime:starttime,
            endtime:endtime,
            searchType:SearchType,
            setIsSearch: setIsSearch
        },
        cache:false,
        complete: function (data) {
            if (data.responseText != 'error' && data.responserText != '') { 
                //判断是进行中还是已结束 start
                var ingCap = document.getElementById("btnLi_0");
                var endCap = document.getElementById("btnLi_1");
                var interCap = document.getElementById("btnLi_2");
                if (ingCap.className == "at") { 
                    orderType = 0;
                }
                else if (endCap.className == "at") { 
                    orderType = 1;
                }
                else if (interCap.className == "at") {
                    orderType = 2;
                }
                //判断是进行中还是已结束 end 

                if (orderType == 0) {
                    $("#infoBox_0").html(data.responseText);
                }
                else if (orderType == 1) {
                    $("#infoBox_1").html(data.responseText);
                }
                else if (orderType == 2) {
                    $("#infoBox_2").html(data.responseText);
                }

                //<<--------------按钮操作

                //点击取消订单
                squl.dom("#moreBtn div.btn_info a.cancelOrder").on("click", function (e) {
                    squl.eventUtil.preventDefault(e);

                    squl.create("UpTier", {
                        width: "585px",
                        height: "478px",
                        iframe: true,
                        href: this.href,
                        inline: false
                    });
                })


                //显示积分宝用户不可以参加验客、点评奖金等
                function showcanNotYk() {
                    squl.create("UpTier",
                        {
                            upCol: "Div1", //弹出控件			
                            exitList: "Img1,A1" //关闭触发器数组用,分割
                        });
                } 

                ///陆兴悦 write 开始

                squl.use("admin", function (squl) {
                    //订单说明
                    mouseBubbleInner("#moreStateTips");

                    //更多操作
                    mouseBubbleInner("#moreBtn span.btn_more", function (elem) {
                        var rWidth = squl.dom(elem).realWidth()[0];
                        var ulWidth = squl.dom(".bubble", elem).realWidth()[0];
                        if (ulWidth <= rWidth - 2) {
                            squl.dom(".bubble", elem).setStyle({ width: rWidth - 2 + "px" });
                            squl.dom("table.tab_1", elem).setStyle({ width: rWidth - 2 + "px" });
                            squl.dom("ul.more_ul", elem).setStyle({ width: rWidth - 2 + "px" });
                        } else {
                            squl.dom("table.tab_1", elem).setStyle({ width: ulWidth - 2 + "px" });
                            squl.dom("ul.more_ul", elem).setStyle({ width: ulWidth - 2 + "px" });
                        }
                    });
                    squl.on("mouseover", function () {
                        squl.dom(this).addClass("li_bg");
                    }, "#moreBtn ul.more_ul li");
                    squl.on("mouseout", function () {
                        squl.dom(this).removeClass("li_bg");
                    }, "#moreBtn ul.more_ul li");

                    squl.on("click", function (e) {
                        squl.eventUtil.preventDefault(e);
                        try {
                            $.colorbox.close();
                        } catch (e) { }
                    }, "#bubbles span.exit_btn, #bubbles a.exit_btn")

                    //常用联系人
                    var txtpassengerName = getOOP("txtpassengerName");
                    txtpassengerName.onkeyup = function (e) {
                        upKey(e, "dimCity_link", this);
                    }
                    txtpassengerName.onblur = function () {
                        getOOP("dimCity_link").style.display = "none";
                    };
                    txtpassengerName.onfocus = function () {
                        cOftenUse(this);
                    } 

                    /**
                    * 补寄行程单      异步请求返回json格式。 
                    * --成功时返回 			'{"state":100, skipUrl:"xxx.aspx"}'	skipUrl为跳转链接
                    * --失败不满足条件 		'{"state":200, tipsText:"xxx"}'	tipsText为提示文字
                    * --已经无法补寄 		'{"state":201, tipsText:"xxx"}'	tipsText为提示文字
                    */
                    squl.dom("#moreBtn div.btn_info a.mailItin").on("click", function (e) {
                        squl.eventUtil.preventDefault(e);
                        try {//请求补寄行程单
                            squl.ajax({
                                url: _flight("mailItinUrl"),
                                data: "orderId=" + this.getAttribute("orderId") + "&fType=AddPostJourneyBill",
                                onDataSuccess: function (data) {
                                    if (data && data.state) {
                                        switch (data.state) {
                                            case 100: //正常
                                                if (data.skipUrl && data.skipUrl !== "") {
                                                    window.location.href = data.skipUrl;
                                                }
                                                break;
                                            case 200: //仍然可以在条件满足后补寄
                                                if (data.tipsText) {
                                                    showTips("补寄行程单", data.tipsText);
                                                }
                                                break;
                                            case 201: //已经无法补寄
                                                if (data.tipsText) {
                                                    showTips("补寄行程单", data.tipsText);
                                                }
                                                break;
                                        }
                                    }
                                }
                            });
                        }
                        catch (e) { }
                    });

                    /**
                    * 取消配送行程单      异步请求返回json格式。 
                    * --成功时返回 			'{"state":100, skipUrl:"xxx.aspx"}'	skipUrl为跳转链接
                    * --已经无法取消 		'{"state":201, tipsText:"xxx"}'	tipsText为提示文字
                    */
                    squl.dom("#moreBtn div.btn_info a.cMailItin").on("click", function (e) {
                        squl.eventUtil.preventDefault(e);
                        try {//请求取消配送行程单
                            squl.ajax({
                                url: _flight("cMailItinUrl"),
                                data: "orderId=" + this.getAttribute("orderId") + "&fType=cMailItinUrl",
                                onDataSuccess: function (data) {
                                    if (data && data.state) {
                                        switch (data.state) {
                                            case 100: //正常
                                                if (data.skipUrl && data.skipUrl !== "") {
                                                    window.location.href = data.skipUrl;
                                                }
                                                break;
                                            case 201: //已经无法取消
                                                if (data.tipsText) {
                                                    showTips("取消配送行程单", data.tipsText);
                                                }
                                                break;
                                        }
                                    }
                                }
                            });
                        }
                        catch (e) { }
                    });

                    /**
                    * 验客      异步请求返回json格式。 
                    * --成功时返回 			'{"state":100, skipUrl:"xxx.aspx"}'	skipUrl为跳转链接
                    * --失败不满足条件 		'{"state":200, tipsText:"xxx"}'	tipsText为提示文字
                    */
                    squl.dom("#moreBtn div.btn_info a.vPassenger").on("click", function (e) {
                        squl.eventUtil.preventDefault(e);
                        try {//验客
                            squl.ajax({
                                url: _flight("vPassengerUrl"),
                                data: "orderId=" + this.getAttribute("orderId") + "&fType=vPassengerUrl",
                                onDataSuccess: function (data) {
                                    if (data && data.state) {
                                        switch (data.state) {
                                            case 100: //正常
                                                if (data.skipUrl && data.skipUrl !== "") {
                                                    window.location.href = data.skipUrl;
                                                }
                                                break;
                                            case 200: //无法满足条件
                                                if (data.tipsText) {
                                                    showTips("提示", data.tipsText);
                                                }
                                                break;
                                        }
                                    }
                                }
                            });
                        }
                        catch (e) { }
                    });
					
					
					/****航班变动日志***/	
					var fChages = squl.dom("#infoBox_0 .flightChange"),
						fcTcc=squl.dom("#lineChange"),
						fcTable=squl.dom("#lineChange .lcList"),
						fcFail = squl.dom("#infoBox_0 .payFaild"),
						fcReason = squl.dom("#clReason"),
						fcclMian = squl.dom("#clReason .clMian");
						
						
						fChages.on("mouseover",function(){
							if(fcTcc[0].style.display=="block"){
								return;
							}
							var _this=this;
							squl.ajax({
									url: "/AjaxHandler/flightajaxinterface.aspx?ftype=getchangedetail",
									data: "orderid="+this.getAttribute("no"),
									onDataSuccess: function (data) {
										if (data && data.state) {
											switch (data.state) {
												case 100: //正常
													var trLength = fcTable[0].getElementsByTagName("tr").length;
													//console.log(fcTcc[0])
													for(var i=1;i<trLength;i++){
														fcTable[0].deleteRow(1);
														
													}
													if(data.detail){
														for(var i=0;i<data.detail.length;i++){
															var tr = fcTable[0].insertRow(i+1);
															var ret=data.detail[i].split("|");
															//var _ret=[];
															for(var j=0;j<ret.length;j++){
																var td = tr.insertCell(j);
																td.innerHTML=ret[j];
															}	
														}
													}
													
													//用$取定位，可以用fish新版替换
													$("#lineChange").css({left:$(_this).offset().left,top:$(_this).offset().top+$(_this).height()+5})
													fcTcc[0].style.display="block";
													break;
											}
										}
									}
							});
						})
					fChages.on("mouseout",function(){
						timer = setTimeout(function(){
							fcTcc[0].style.display="none";
						},500)
						
					})
					
					 fcFail.on("mouseover",function(){
						if(fcReason[0].style.display == "block"){
							return;
						}
						var _this=this;
						squl.ajax({
                                url: "/AjaxHandler/flightajaxinterface.aspx?ftype=GetPayError",
                                data: "orderid="+this.getAttribute("no"),
                                onDataSuccess: function (data) {
                                    if (data && data.state) {
                                        switch (data.state) {
                                            case 100: //正常
												if(data.log){
													fcclMian[0].innerHTML=data.log;
													fcReason[0].style.display="block";
													//用$取定位，可以用fish新版替换
													$("#clReason").css({left:$("#ingordertable").offset().left+520,top:$(_this).offset().top+$(_this).height()-10})
												}	
                                                break;
                                        }
                                    }
                                }
                        });
					 })
					
					 fcFail.on("mouseout",function(){
						fcReason[0].style.display="none";
					 })
					
                    //按钮操作-------------->>
                })



                //<<-------常用联系人 START-------
                //脚本文件头部声明公共变量
                var useCityNumber = -1;
                //<联系人文本框的获取光标事件> //绑定出现 鼠标经过样式 点击传值
                function cOftenUse(that, type) {
                    var dimCity_link = document.getElementById("dimCity_link");
                    //kares
                    dimCity_link.style.display = "block";
                    //只显示对应类型的
                    //squl.dom("#dimCity_link tr").setStyle({ "display": "none" });
                    //squl.dom("#dimCity_link tr." + type).setStyle({ "display": "block" });

                    setTimeout(function () {
                        var realWidth = squl.dom("#dimCity_table").realWidth()[0];
                        var realHeight = squl.dom("#dimCity_table").realHeight()[0];
                        squl.dom("#dimCity_frame").setStyle({ width: realWidth });
                        squl.dom("#dimCity_frame").setStyle({ height: realHeight });
                    }, 1);

                    that.parentNode.appendChild(dimCity_link);
                    useCityNumber = -1;
                    dimCity_link.style.display = "block";
                    tr = dimCity_link.getElementsByTagName("tr");
                    for (var i = 0; i < tr.length; i++) {
                        squl.dom(tr[i]).addClass("aOut");
                        squl.dom(tr[i]).removeClass("aHover");
                    }
                    for (var i = 0; i < tr.length; i++) {
                        tr[i].onmouseover = function () {
                            squl.dom(this).removeClass("aOut");
                            squl.dom(this).addClass("aHover");
                        }
                        tr[i].onmouseout = function () {
                            squl.dom(this).addClass("aOut");
                            squl.dom(this).removeClass("aHover");
                        }
                        tr[i].onmousedown = function () {

                            setValueAndCheck(this, that);

                        }
                    }
                }

                //鼠标失去焦点后将改变值设为0
                squl.on("mouseout", function () { useCityNumber = -1; }, "#dimCity_link");
                //鼠标移入改变样式
                squl.on("mouseover", function (e, param) {
                    var div = document.getElementById("dimCity_link");
                    if (div) {
                        var t = div.getElementsByTagName("tr");
                        if (div.style.display == "" || div.style.display == "block" || div.style.display == "inline") {
                            var b = squl.eventUtil.getTarget(squl.eventUtil.getEvent(e));
                            while (b && b.localName != "body") {
                                b = b.parentNode;
                                if (b != null) {
                                    if (b == div) {
                                        if (useCityNumber >= 0 && useCityNumber < t.length) {
                                            squl.dom(t[useCityNumber]).addClass("aOut");
                                            squl.dom(t[useCityNumber]).removeClass("aHover");
                                        }
                                        useCityNumber = -1;
                                    }
                                }
                            }; //while
                        }
                    }
                }, "#dimCity_link");

                //键盘上下 方向键绑定数据
                function upKey(e, bgid, txtValue) {
                    var code;
                    var div = document.getElementById(bgid);
                    if (div) {
                        var t = div.getElementsByTagName("tr");
                        if (div.style.display == "" || div.style.display == "inline" || div.style.display == "block") {
                            if (!e) {
                                var e = window.event;
                            }
                            if (e.keyCode) {
                                code = e.keyCode;
                            }
                            else if (e.which) {
                                code = e.which;
                            }
                            if (code == 40) {
                                if (useCityNumber >= t.length - 1) {
                                    useCityNumber = t.length - 1;
                                }
                                else {
                                    useCityNumber++;
                                }
                            }

                            if (code == 38) {
                                if (useCityNumber <= 0) {
                                    useCityNumber = 0;
                                }
                                else {
                                    useCityNumber--;
                                }
                            }
                            if (code == 13) {
                                div.style.display = "none";
                                useCityNumber = -1;
                            }
                            if (code == 38 || code == 40) {
                                updateList(t, txtValue);
                            }
                        }
                    }
                }

                //处理模糊查询信息
                function updateList(list, txt) {
                    for (var i = 0; i < list.length; i++) {
                        squl.dom(list[i]).addClass("aOut");
                        squl.dom(list[i]).removeClass("aHover");
                    }
                    if (useCityNumber >= 0 && useCityNumber < list.length) {
                        squl.dom(list[useCityNumber]).removeClass("aOut");
                        squl.dom(list[useCityNumber]).addClass("aHover");
                        setValueAndCheck(list[useCityNumber], txt);
                    }
                }

                function setValueAndCheck(trElem, inputE) {
                    var this_td = trElem.getElementsByTagName("td");
                    var pNode = trElem.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
                    inputE.value = this_td[0].innerHTML;
                }
                //-------常用联系人 END------->> 

                ///陆兴悦 write 结束

                timeIng() ;
                publicYk();
            }
            ajacInfo = null;
        }
    });
	
	
}

function mouseBubbleInner(tarQuery, focusCall, blurCall){
    squl.on("mouseover", function(e){
        var related = squl.eventUtil.getRelated(e);
		if(!related){
			return;
		}
        var p = related.parentNode;
        while(p !== this && p !== null){
            p = p.parentNode;
        }
        if(p = this){
			squl.dom(".text", p).addClass("textH");
            squl.dom(".bubble", p).addClass("bubbleH");
			if(focusCall){
				focusCall(this);
			}
        }
    }, tarQuery);

    squl.on("mouseout", function(e){
        var related = squl.eventUtil.getRelated(e);
		if(!related){
			return;
		}
        var p = related.parentNode;
        while(p !== this && p !== null){
            p = p.parentNode;
        }
        if(p = this){
			squl.dom(".text", p).removeClass("textH");
            squl.dom(".bubble", p).removeClass("bubbleH");
			if(blurCall){
				blurCall(this);
			}
			if(squl.dom("#btnLi_2")[0].className == "at"){
				squl.dom("#InterStatus table.orderinter_info")[0].style.display = "";
				squl.dom("#InterStatus table.orderdomestic_info")[0].style.display = "none";
			}else{
				squl.dom("#InterStatus table.orderinter_info")[0].style.display = "none";
				squl.dom("#InterStatus table.orderdomestic_info")[0].style.display = "";
			}
        }
    }, tarQuery);
}

function showTips(title, text, callBack){
	squl.dom("#tipsBubble h3").set("innerHTML", title);
	squl.dom("#tipsBubble div.jp_01").set("innerHTML", text);
	squl.dom("#tipsBubble a.jp_btn01")[0].onclick = function(){
		$.colorbox.close();
		if(callBack){
			callBack();
		}
	}
	squl.create("UpTier", {
		upCol:"tipsBubble"
	})
}

function showVerify(title, text, callBack){
	squl.dom("#verifyBubble h3").set("innerHTML", title);
	squl.dom("#verifyBubble div.p03").set("innerHTML", text);
	squl.dom("#verifyBubble a.confirm_btn")[0].onclick = function(){
		$.colorbox.close();
		if(callBack){
			callBack();
		}
	}
	squl.create("UpTier", {
		upCol:"verifyBubble"
	})

}


squl.use("admin", function(squl){
	//订单说明
	mouseBubbleInner("#moreStateTips");
	
	
	
	//更多操作
	mouseBubbleInner("#moreBtn span.btn_more", function(elem){
		var rWidth = squl.dom(elem).realWidth()[0];
		var ulWidth = squl.dom(".bubble", elem).realWidth()[0];
		if(ulWidth<=rWidth-2){
			squl.dom(".bubble", elem).setStyle({width:rWidth-2+"px"});
			squl.dom("table.tab_1",elem).setStyle({width:rWidth-2+"px"});
			squl.dom("ul.more_ul",elem).setStyle({width:rWidth-2+"px"});
		}else{
			squl.dom("table.tab_1",elem).setStyle({width:ulWidth-2+"px"});
			squl.dom("ul.more_ul",elem).setStyle({width:ulWidth-2+"px"});
		}	
	});
	squl.on("mouseover", function(){
		squl.dom(this).addClass("li_bg");
	}, "#moreBtn ul.more_ul li");
	squl.on("mouseout", function(){
		squl.dom(this).removeClass("li_bg");
	}, "#moreBtn ul.more_ul li"); 
	
	squl.on("click", function(e){
		squl.eventUtil.preventDefault(e);
		try{
			$.colorbox.close();
		}catch(e){}
	}, "#bubbles span.exit_btn, #bubbles a.exit_btn")
	
	//常用联系人
	var txtpassengerName = getOOP("txtpassengerName");
    txtpassengerName.onkeyup = function (e) {
        upKey(e, "dimCity_link", this);
    }
    txtpassengerName.onblur = function () {
		getOOP("dimCity_link").style.display = "none";
    };
	txtpassengerName.onfocus = function(){
		cOftenUse(this);
	}
		                    
	//搜索时间控件禁止使用鍵盤,鼠標
	squl.banOperate("txtstartTime");
	squl.banOperate("txtendTime");
	//綁定默認日期
	//squl.dom("#txtstartTime").setValue(squl.getTime());
	//squl.dom("#txtendTime").setValue(squl.getTime());
	
	squl.on("click", function()
	{
//	    squl.create("DateTime",
//	    {
//	        funObj: soso_upTime2,
//			isOverTime : true,
//	        starTime: squl.getTime({year: -10}),
//	        append: "startTimeCal",//绑定日期控件外层ID
//	        txtTime: "txtstartTime", //要显示日期的文本框
//	        pClass: "date_list" //样式继承最高级样式
//	    });

		fish.date.exec({
        	funObj: soso_upTime2,
	        starTime: squl.getTime({year: -10}),
	        isOverTime:true,
            txtTime: "#txtstartTime"
        });
	}, "#txtstartTime");
	function soso_upTime2()
	{
	    document.getElementById("txtstartTime").style.color = "#999";
	    squl.dom("#txtendTime").setValue(squl.getTime(
	    {
	        date: 1,
	        time: document.getElementById("txtstartTime").value
	    }));
		var stime = squl.getTime({date:-1,time: document.getElementById("txtstartTime").value});
	
//	    squl.create("DateTime",
//	    {
//	        funObj: function(){},
//	        starTime: stime,
//			isOverTime : true,
//			endTime:squl.getTime({year:1, time: stime}),
//	        append: "endTimeCal",//绑定日期控件外层ID
//	        txtTime: "txtendTime", //要显示日期的文本框
//	        pClass: "date_list" //样式继承最高级样式
//	    });
		fish.date.exec({
        	funObj: function(){},
	        starTime: squl.getTime({year: -10}),
	        isOverTime:true,
            endTime: squl.getTime({year:1, time: stime}),
            txtTime: "#txtendTime"
        });
	};
	squl.on("click", function(e)
	{
	    squl.ValidaClick(e, "startTimeCalDateTime", "txtstartTime");
	}, document);
	//返回日期
	squl.on("click", function()
	{
		var starTime = squl.getTime({date:-1,time: document.getElementById("txtstartTime").value});
	
//	    squl.create("DateTime",
//	    {
//	        funObj: function(){},
//			isOverTime : true,
//			starTime:starTime,
//			endTime:squl.getTime({year:1, time: starTime}),
//	        append: "endTimeCal",//绑定日期控件外层ID
//	        txtTime: "txtendTime", //要显示日期的文本框
//	        pClass: "date_list" //样式继承最高级样式
//	    });

		fish.date.exec({
        	funObj: function(){},
	        starTime: squl.getTime({year: -10}),
	        isOverTime:true,
            endTime: squl.getTime({year:1, time: starTime}),
            txtTime: "#txtendTime"
        });
	}, "#txtendTime");
	squl.on("click", function(e)
	{
	    squl.ValidaClick(e, ["endTimeCalDateTime", "txtendTime","startTimeCalDateTime"]);
	}, document);

	
	
	
	/**
	 * 补寄行程单      异步请求返回json格式。 
	 * --成功时返回 			'{"state":100, skipUrl:"xxx.aspx"}'	skipUrl为跳转链接
	 * --失败不满足条件 		'{"state":200, tipsText:"xxx"}'	tipsText为提示文字
	 * --已经无法补寄 		'{"state":201, tipsText:"xxx"}'	tipsText为提示文字
	 */
	squl.dom("#moreBtn div.btn_info a.mailItin").on("click", function(e){
		squl.eventUtil.preventDefault(e);
		try {//请求补寄行程单
			squl.ajax({
			    url: _flight("mailItinUrl"),
			    data: "orderId=" + this.getAttribute("orderId") + "&fType=AddPostJourneyBill",
				onDataSuccess:function(data){
					if(data && data.state){
						switch(data.state){
							case 100://正常
								if(data.skipUrl && data.skipUrl !== ""){
									window.location.href = data.skipUrl;
								}
								break;
							case 200://仍然可以在条件满足后补寄
								if(data.tipsText){
									showTips("补寄行程单", data.tipsText);
								}
								break;
							case 201://已经无法补寄
								if(data.tipsText){
									showTips("补寄行程单", data.tipsText);
								}
								break;
						}
					}
				}
			});
		}
		catch(e){}
	});
	
	/**
	 * 取消配送行程单      异步请求返回json格式。 
	 * --成功时返回 			'{"state":100, skipUrl:"xxx.aspx"}'	skipUrl为跳转链接
	 * --已经无法取消 		'{"state":201, tipsText:"xxx"}'	tipsText为提示文字
	 */
	squl.dom("#moreBtn div.btn_info a.cMailItin").on("click", function(e){
		squl.eventUtil.preventDefault(e);
		try {//请求取消配送行程单
			squl.ajax({
				url: _flight("cMailItinUrl"),
				data: "orderId=" + this.getAttribute("orderId") + "&fType=cMailItinUrl",
				onDataSuccess:function(data){
					if(data && data.state){
						switch(data.state){
							case 100://正常
								if(data.skipUrl && data.skipUrl !== ""){
									window.location.href = data.skipUrl;
								}
								break;
							case 201://已经无法取消
								if(data.tipsText){
									showTips("取消配送行程单", data.tipsText);
								}
								break;
						}
					}
				}
			});
		}
		catch(e){}
	});
	
	/**
	 * 验客      异步请求返回json格式。 
	 * --成功时返回 			'{"state":100, skipUrl:"xxx.aspx"}'	skipUrl为跳转链接
	 * --失败不满足条件 		'{"state":200, tipsText:"xxx"}'	tipsText为提示文字
	 */
	squl.dom("#moreBtn div.btn_info a.vPassenger").on("click", function(e){
		squl.eventUtil.preventDefault(e);
		try {//验客
			squl.ajax({
				url: _flight("vPassengerUrl"),
				data: "orderId=" + this.getAttribute("orderId") + "&fType=vPassengerUrl",
				onDataSuccess:function(data){
					if(data && data.state){
						switch(data.state){
							case 100://正常
								if(data.skipUrl && data.skipUrl !== ""){
									window.location.href = data.skipUrl;
								}
								break;
							case 200://无法满足条件
								if(data.tipsText){
									showTips("提示", data.tipsText);
								}
								break;
						}
					}
				}
			});
		}
		catch(e){}
	});
	
	//按钮操作-------------->>
})


//航班变动弹出层事件
squl.dom("#lineChange").on("mouseover",function(e){
	squl.eventUtil.stopBubble(e);
	if(timer){
		clearTimeout(timer);
	}
	this.style.display="block";
})
squl.dom("#lineChange").on("mouseout",function(e){
	squl.eventUtil.stopBubble(e);
	this.style.display="none";
})

//从aspx页面中移过来的 start
//隐藏进行中的订单table
function hidIngTable() {
    document.getElementById("ingordertable").style.display = "none";
    document.getElementById("NoIngOrderTable").style.display = "block";
}

//隐藏已结束的订单tabel 
function hidEndTable() {
    document.getElementById("endordertable").style.display = "none";
    document.getElementById("NoEndOrderTable").style.display = "block";
}

//取消订单
function CancelOrder() {
    var btnSearch = document.getElementById("btnSearch");
    document.getElementById("cancelOrderDiv").style.display = "block";
    document.getElementById("cancelOrderDiv").style.top = PointsPos(btnSearch, "Top") + 140 + "px";
    document.getElementById('cancelOrderDiv').style.left = PointsPos(btnSearch, "Left") + -570 + "px";
}

//关闭弹窗
function CloseDiv(id) {
    document.getElementById(id).style.display = "none";
}

//获取相对位置
function PointsPos(el, ePro) {
    var ePos = 0;
    while (el != null) {
        ePos += el["offset" + ePro];
        el = el.offsetParent;
    }
    return ePos;
}
//从aspx页面中移过来的 end

//添加付款时间倒计时
function timeIng() {
	var ti_time = squl.dom("#ingordertable div.ti_time");
	var _h;
	var _m;
	var _s;
	var payB;
	
	
	var counter = null;
	// var i = 0；
	
	for(var n=0; n < ti_time.length ; n++){
		
		_h = squl.dom("span.time_h", ti_time[n])[0];
		_m = squl.dom("span.time_m", ti_time[n])[0];
		_s = squl.dom("span.time_s", ti_time[n])[0];
		payB = squl.dom("a.btn01_b", ti_time[n].parentNode)[0];
		
		var h = parseInt(_h.innerHTML);
		var m = parseInt(_m.innerHTML);
		var s = parseInt(_s.innerHTML);
	
		f(_s, _m, _h, s, m, h , payB);		
	}

	function f(_s, _m, _h, s, m, h ,payB){
		counter = null;
		var f = arguments.callee;
		counter = setTimeout(
		function(){
			s -= 1;
			if ((s < 0) && (m > 0 || h > 0)) {
				s = 59;
				m -= 1;
				if ((m < 0) && (h > 0)) {
					m = 59;
					h -= 1;
					if (h < 0) {
						h = 0;
					}
				}
			}
			else 
				if ((s < 0) && (m <= 0) && (h <= 0)) {
					s = 0;
					counter = null;
					payB.className = "btn01_l";
					payB.setAttribute("href", "javascript:void(0);");
					return;
				}
			_s.innerHTML = s;
			_m.innerHTML = m;
			_h.innerHTML = h;
			f(_s, _m, _h, s, m, h ,payB);
		// console.log(i);
		// i++;
		}, 1000);
	}
}
//机票
function publicYk() {
    squl.on("mouseover", function () {

        var num = this.id.substring(10);
        document.getElementById("showfyk_" + num).style.display = "block";

    }, "#infoBox_1 a.publicyk");
    squl.on("mouseout", function () {

        var num = this.id.substring(10);
        document.getElementById("showfyk_" + num).style.display = "none";

    }, "#infoBox_1 a.publicyk");
    squl.on("mouseover", function () {
        this.style.display = "block";

    }, "#infoBox_1 div.yw_Hout");
    squl.on("mouseout", function () {
        this.style.display = "none";

    }, "#infoBox_1 div.yw_Hout");
}
publicYk();