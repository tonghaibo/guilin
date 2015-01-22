String.prototype.trim = function () { return this.replace(/(^\s*)|(\s*$)/g, ""); }

function getOOP(idName) {
    return document.getElementById(idName);
}

function getChildList(parentId, childName) {
    return document.getElementById(parentId).getElementsByTagName(childName);
}

function checkList(selBox, selCher, selErr) {
    var checkType = false;
    var chList = getChildList(selBox, selCher);
    for (i = 0; i < chList.length; i++) {
        if (chList[i].checked == true) {
            checkType = true;
            break;
        }
        chList[i].onclick = function () { getOOP(selErr).style.display = "none"; }
    }
    if (checkType) {
        getOOP(selErr).style.display = "none";
    } else {
        getOOP(selErr).style.display = "block";
    }
    return checkType;
}

function checkSel(selBox, errBox) {
    var selIndex = getOOP(selBox).selectedIndex;
    var errInfo = getOOP(errBox);
    if (selIndex == 0) {
        errInfo.style.display = "block";
        return false;
    } else {
        errInfo.style.display = "none";
        return true;
    }
}

function checkMail(mailNum, errBox) {
    var mailValue = getOOP(mailNum).value.trim();
    var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    if (mailValue == null || mailValue == "") {
        getOOP(errBox).style.display = "block";
        getOOP(errBox).innerHTML = "请填写E-mail地址";
        return false;
    } else {
        if (!myreg.test(mailValue)) {
            getOOP(errBox).style.display = "block";
            getOOP(errBox).innerHTML = "请正确填写E-mail地址";
            return false;
        } else {
            $.ajax({
                type: 'GET',
                url: '/AjaxHandler/DujiaAjaxInterface.aspx?',
                data: 'action=regAdress&mail=' + mailValue + '&date=' + new Date(),
                complete: function (data) {
                    if (data.responseText != '' && data.responserText != '-1') {
                        if (data.responseText == "true") {
                            getOOP(errBox).style.display = "none";
                            return true;
                        }
                        else {
                            getOOP(errBox).style.display = "block";
                            getOOP(errBox).innerHTML = "邮箱地址已存在";
                            return false;
                        }
                    }
                    else {
                        getOOP(errBox).style.display = "block";
                        getOOP(errBox).innerHTML = "请正确填写E-mail地址";
                        return false;
                    }
                }
            });
            //            getOOP(errBox).style.display = "none";
            //            return true;
        }
    }
}

function checkSex(parentId, errBox) {
    var radioList = getChildList(parentId, "input");
    var str = "";
    for (var i = 0; i < radioList.length; i++) {
        if (radioList[i].checked) {
            str = radioList[i].value;
        }

        radioList[i].onclick = function () {
            getOOP(errBox).style.display = "none";
        }
    }
    if (str == "") {
        getOOP(errBox).style.display = "block";
        return false;
    } else {
        getOOP(errBox).style.display = "none";
        return true;
    }
}

function checkDate() {
    var selBox1 = getOOP("ddlBirthDayOfYear").selectedIndex;
    var selBox2 = getOOP("ddlBirthDayOfMonth").selectedIndex;
    var selBox3 = getOOP("ddlBirthDayOfDay").selectedIndex;
    var errBoxs = getOOP("BirthDayErr");
    if (selBox1 == 0 || selBox2 == 0 || selBox3 == 0) {
        errBoxs.style.display = "block";
        return false;
    } else {
        errBoxs.style.display = "none";
        return true;
    }
}

function checkAddress() {
    var selBox1 = getOOP("ddlProvinceId").selectedIndex;
    var selBox2 = getOOP("ddlCityId").selectedIndex;
    var errBoxs = getOOP("addressErr");
    if (selBox1 == 0 || selBox2 == 0) {
        errBoxs.style.display = "block";
        return false;
    } else {
        errBoxs.style.display = "none";
        return true;
    }
}

function showSuccessAlert() {
    squl.create("UpTier",
        {
            upCol: "successBox", //弹出控件			
            exitList: "exit3,exit4" //关闭触发器数组用,分割
        });
}

function showFailAlert() {
    squl.create("UpTier",
    {
        upCol: "failBox", //弹出控件			
        exitList: "exit5,exit6" //关闭触发器数组用,分割
    });
}




/////////////////////////////////////////////////////////////
function checkUseName() {
    var useName = getOOP("userName").value;
    var useErr = getOOP("nameErr");
    var myReg = /^ +$/;
    var reg = /^([a-zA-Z\u4e00-\u9fa5]* ?)*[a-zA-Z\u4e00-\u9fa5]*$/;

    if (useName == "" || useName == null || myReg.test(useName)) {
        useErr.style.display = "block";
        useErr.innerHTML = "请填写真实姓名";
        return false;
    } else if (!(reg.test(useName))) {
        useErr.style.display = "block";
        useErr.innerHTML = "＂真实姓名＂只允许使用 中文、26个英文字母的组合形式!";
        return false;
    } else {
        useErr.style.display = "none";
        return true;
    }
}

function checkNC() {
    var useName = getOOP("NCName").value;
    var useErr = getOOP("ncErr");
    var myReg = /^ +$/;
    var reg2 = /(<[b|B][r|R]\/*>)+|(<[p|P](.|\n)*?>)/;
    var reg3 = /(\s*&[n|N][b|B][s|S][p|P];\s*)+/;
    var reg4 = /<(.|\n)*?>/;
    var reg5 = /'/;

    if (useName == "" || useName == null || myReg.test(useName)) {
        useErr.style.display = "block";
        useErr.innerHTML = "请填写昵称";
        return false;
    } else if (useName.length > 16) {
        useErr.style.display = "block";
        useErr.innerHTML = "昵称最多15个字！";
        return false;
    } else if (useName.match(reg2) || useName.match(reg3) || useName.match(reg4) || useName.match(reg5)) {
        useErr.style.display = "block";
        useErr.innerHTML = "包含非法字符，请重新输入昵称！";
        return false;
    } else {
        useErr.style.display = "none";
        return true;
    }
}

/////////////////////////////////////////////////////////////

function checkSub() {
    if (checkList("sel", "input", "selErr") & checkList("cause", "input", "causeErr") & checkList("payList", "input", "payErr") & document.getElementById("mailErr").style.display=="none" & checkSel("job", "jobErr") & checkSel("study", "studyErr") & checkAddress() & checkDate() & checkSex("sexBox", "errSex") & checkNC() & checkUseName()) {
        return true;
    } else {
        return false;
    }
}

getOOP("userName").onblur = function () {
    checkUseName();
}

getOOP("NCName").onblur = function () {
    checkNC();
}

getOOP("eMail").onblur = function () {
    checkMail("eMail", "mailErr");
}

var liList = getChildList("LiList", "li");
for (i = 0; i < liList.length; i++) {
    if (liList[i].className != "menu_at") {
        liList[i].onmouseover = function () { this.className = 'at'; };
        liList[i].onmouseout = function () { this.className = ''; };
    }
}


getOOP("mySub").onclick = function () {
    if (checkSub()) {
        return true;
    } else {
        return false;
    }
}

//////////////////////////////////

//判断每月天数
function getDays(month, year) {
    var daysInMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    if (1 == month) {
        return ((0 == year % 4) && (0 != (year % 100))) || (0 == year % 400) ? 29 : 28;
    } else {
        return daysInMonth[month];
    }
}
function toInt(s, d) {
    var t;
    return isNaN(t = parseInt(s)) ? d : t;
}
function fnAdd(oListbox, sName, sValue) {
    var oOption = document.createElement("option");
    oOption.appendChild(document.createTextNode(sName));
    if (arguments.length == 3) {
        oOption.setAttribute("value", sValue);
    }
    oListbox.appendChild(oOption);
}
function ChangeBirthDay(idYear, idMonth, idDay) {
    var objYear = document.getElementById(idYear);
    var objMonth = document.getElementById(idMonth);
    var objDay = document.getElementById(idDay);
    var selectDay = objDay.options[objDay.selectedIndex].value;

    //获取指定年月的天数
    var dayCount = getDays((toInt(objMonth.options[objMonth.selectedIndex].value) - 1), toInt(objYear.options[objYear.selectedIndex].value));

    //重新绑定
    objDay.options.length = 0;
    fnAdd(objDay, "--请选择--", "0");
    for (var i = 1; i <= dayCount; i++) {
        fnAdd(objDay, i, i);
    }

    if (toInt(selectDay) > toInt(dayCount)) {
        selectDay = 1; //如果当前月没有这一天则默认选择一号
    }
    objDay.options[selectDay].selected = true;
    checkDate();
}
function ChangeProvince(idProvince, idCity) {
    var objProvince = document.getElementById(idProvince);
    var objCity = document.getElementById(idCity);
    var provinceID = objProvince.options[objProvince.selectedIndex].value;
    $.ajax({
        type: 'GET',
        url: '/Member/MemberInfomation.aspx?',
        data: 'Type=GetCity&Pid=' + provinceID + '&date=' + new Date(),
        complete: function (data) {
            if (data.responseText != '' && data.responserText != '-1') {
                var arrCitys = data.responseText.split(",");
                objCity.options.length = 0;
                fnAdd(objCity, "--请选择市--", "0");
                for (var j = 0; j < arrCitys.length; j++) {
                    var arrCity = arrCitys[j].split("|");
                    fnAdd(objCity, arrCity[0], arrCity[1]);
                }
            }
            else {
                objCity.options.length = 0;
                fnAdd(objCity, "--请选择市--", "0");
            }
        }
    });
    document.getElementById("hidProvinceID").value = objProvince.options[objProvince.selectedIndex].value;
    checkAddress();
}
function ChangeCity(idCity) {
    var objCity = document.getElementById(idCity);
    document.getElementById("hidCityID").value = objCity.options[objCity.selectedIndex].value;
    checkAddress();
}
function ModifyMemberInfo(idName) {
    document.getElementById(idName).style.display = "none";
    document.getElementById("editMemberContent").style.display = "block";
}

/////////////////////////////////////
squl.on("mouseover", function () {
    squl.dom("#mySub").addClass("btn001");
}, "#mySub");

squl.on("mouseout", function () {
    squl.dom("#mySub").removeClass("btn001");
}, "#mySub");

getOOP("job").onchange = function () {
    checkSel("job", "jobErr");
}

getOOP("study").onchange = function () {
    checkSel("study", "studyErr")
}

getOOP("ddlBirthDayOfDay").onchange = function () {
    checkDate();
}