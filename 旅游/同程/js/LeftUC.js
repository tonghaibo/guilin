//异步数据请求
var leftMenu_xmlhttp;
function GetOrderLeftInfo() {
    var typeId = document.getElementById("ucMenu_hidTypeId").value;
    var url = "/AjaxHandler/PublicAjaxInterface.aspx?ft=GetOrderLeftInfo&typeId=" + typeId;
    leftMenu_xmlhttp = leftMenu_CreateXmlhttp();
    leftMenu_xmlhttp.open("post", url);
    leftMenu_xmlhttp.onreadystatechange = leftMenu_MessageBack;
    leftMenu_xmlhttp.send(null);
};
function leftMenu_MessageBack() {
    if (leftMenu_xmlhttp && leftMenu_xmlhttp.readyState == 4 && leftMenu_xmlhttp.status == 200) {
        var value = leftMenu_xmlhttp.responseText;
        if (value != null && value != "") {
            var listValue = value.split("|");
            document.getElementById("MyOrder").innerHTML = listValue[0];
            document.getElementById("ulMyInfo").innerHTML = listValue[1];
            var liList = getChildList("LiList", "li");
            for (i = 0; i < liList.length; i++) {
                if (liList[i].className != "menu_at") {
                    liList[i].onmouseover = function () { this.className = 'at'; };
                    liList[i].onmouseout = function () { this.className = '' };
                }
            }
        }
    }
};
//创建http请求
function leftMenu_CreateXmlhttp() {
    if (window.ActiveXObject) {
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    else if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    }
};
function getChildList(parentId, childName) {		//根据ID获得子项列表
    return document.getElementById(parentId).getElementsByTagName(childName);
}