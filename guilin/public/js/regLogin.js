var conf = {
    email:/^[0-9a-z][_.0-9a-z-]{0,31}@([0-9a-z][0-9a-z-]{0,30}\.){1,4}[a-z]{2,4}$/,
    tel:/^1\d{10}$/,
    pass:/^\S{6,20}$/,
    ver:/^[0-9a-zA-Z]/,
    vcode:/^\w{5}$/
};
var lformTip = {
    '1':'<span class="li-fail-span">请输入正确的账号信息。</span>',
    '2':'<span class="li-fail-span">6位到20位，英文字母、数字或符号。</span>',
    '3':'<span class="li-fail-span">登录失败，用户名或密码错误。</span>',
    '4':'<span class="li-fail-span">用户名不存在。</span>',
    '7':'<span class="li-fail-span">验证码输入错误。</span>',
    'ok':'<span class="li-ok-span"></span>'
}
var rformTip = {
    '1':'<span class="li-fail-span">请输入正确的账号信息。</span>',
    '2':'<span class="li-fail-span">6位到20位，英文字母、数字或符号。</span>',
    '3':'<span class="li-fail-span">您先后输入的密码不一致。</span>',
    '4':'<span class="li-fail-span">已经是注册用户，请<a href="/login">直接登录</a>。</span>',
    '7':'<span class="li-fail-span">验证码输入错误。</span>',
    'ok':'<span class="li-ok-span"></span>'
}
//login input focus
function linputFocus(t){
    var obj = $(t);
    obj.addClass('li-input-click');
    $('#'+obj.attr('name')+'Tip').hide();
}
function linputBlur(t){
    var obj = $(t);
    obj.removeClass('li-input-click');
}
function rinputLoginName(t){
    var obj = $(t);
    obj.addClass('li-input-click');
    var thisTitle = obj.attr('title');
    $('#'+obj.attr('name')+'Tip').html('<span class="li-one-span">'+thisTitle+'</span>');
}
function rinputFocus(t){
    var obj = $(t);
    obj.addClass('li-input-click');
    var thisTitle = obj.attr('title');
    if(undefined != thisTitle){
        $('#'+obj.attr('name')+'Tip').html('<span class="li-Gray-span">'+thisTitle+'</span>');
    }
}
function rinputBlur(t){
    var obj = $(t);
    obj.removeClass('li-input-click');
}

//login show tip
function lshowTip(htmlId,tipKey){
    $('#'+htmlId).html(lformTip[tipKey]);
    $('#'+htmlId).show();
}
//register show tip
function rshowTip(htmlId,tipKey){
    $('#'+htmlId).html(rformTip[tipKey]);
    $('#'+htmlId).show();
}
//用户名检测
function verLoginName(loginName){
    if(loginName==''){
        return false;
    }
    if(conf.email.test(loginName)){
        return true;
    }
	//暂且去掉手机注册的
	/*else if(conf.tel.test(loginName)){
        return true;
    }*/
    return false;
}
function verRegPass(pass){
    if(conf.pass.test(pass)){
        return true;
    }
    return false;
}
function verVcode(vcode){
    if(conf.vcode.test(vcode)){
        return true;
    }
    return false;
}
function lcLoginName(loginName){
    if(!verLoginName(loginName)){
        lshowTip('loginNameTip',1);
        return false;
    }
    lshowTip('loginNameTip','ok');
    return true;
}
function lcPass(pass){
    if(!verRegPass(pass)){
        lshowTip('passwordTip',2);
        return false;
    }
    lshowTip('passwordTip','ok');
    return true;
}
function lcVcode(vcode){
    if(!verVcode(vcode)){
        rshowTip('codeTip',7);
        return false;
    }
    rshowTip('codeTip','ok');
    return true;
}
function rcLoginName(loginName){
    if(!verLoginName(loginName)){
        rshowTip('loginNameTip',1);
        return false;
    }
    rshowTip('loginNameTip','ok');
    return true;
}

function rcPass(pass){
    if(!verRegPass(pass)){
        rshowTip('passwordTip',2);
        return false;
    }
    rshowTip('passwordTip','ok');
    return true;
}

function rcRepass(pass){
    if(!verRegPass(pass)){
        rshowTip('repasswordTip',2);
        return false;
    }
    if(pass != document.getElementById('password').value){
        rshowTip('repasswordTip',3);
        return false;
    }
    rshowTip('repasswordTip','ok');
    return true;
}
function rcVcode(vcode){
    if(!verVcode(vcode)){
        rshowTip('codeTip',7);
        return false;
    }
    rshowTip('codeTip','ok');
    return true;
}

function checkIsReg(loginName){
    if(verLoginName(loginName)){
        $.ajax({
            url: 'index_check.html',
            data: {
                loginName:loginName
            },
			type:'post',
            success: function(staus){
                if(staus==1){
                    rshowTip('loginNameTip',4);
                }else if(staus==0){
                    rshowTip('loginNameTip','ok');
                }else{
                    rshowTip('loginNameTip',1);
                }
            }
        });
    }else{
        rshowTip('loginNameTip',1);
    }
}

function submitLoginForm(){
    var verLoginName = lcLoginName(document.getElementById('loginName').value);
    var verPass = lcPass(document.getElementById('password').value);
    if(verLoginName && verPass){
        document.getElementById('loginform').submit();
    }
}
function rcAgree(t){
    var isAgree = t.checked;
    if(!isAgree){
        document.getElementById('notRegSubmit').style.display="block";
        document.getElementById('canRegSubmit').style.display="none";
        document.getElementById('mustAgreetk').style.display="block";
    }else{
        document.getElementById('notRegSubmit').style.display="none";
        document.getElementById('canRegSubmit').style.display="block";
        document.getElementById('mustAgreetk').style.display="none";
    }
}
function submitRegForm(){
    var verLoginName = rcLoginName(document.getElementById('loginName').value);
    var passV = document.getElementById('password').value;
    var verPass = rcPass(passV);
    var verRePass = rcRepass(passV);
    var verCode = rcVcode(document.getElementById('code').value);
    if(verLoginName && verPass && verRePass && verCode){
        document.getElementById('regform').submit();
    }
}
function sendMailTip(loginName,tempnum){
    switch(tempnum){
		case 4:
        case 1:
            $.ajax({
                url: 'index_sendmail.html',
                type:'POST',
                data: {
                    mail:loginName,
                    tempnum:tempnum
                },
                dataType:"json",
                success: function(res){
                    var status = res.status;
					var tipObj = document.getElementById('sendMailTip');
                    switch(status){
                        case 0:
                            tipObj.innerHTML = '邮件发送成功。';
                            break;
                        case 1:
                            tipObj.innerHTML = '邮件发送失败。';
                            break;
                        case 2:
                            tipObj.innerHTML = '此注册邮箱不存在。';
                            break;
						case 3:
                            tipObj.innerHTML = '邮件发送失败，请重试。';
                            break;
						case 4:
                            tipObj.innerHTML = '请不要反复发送邮件。';
                            break;
                    }
                    tipObj.style.display="block";
                    setTimeout(function(){
                        tipObj.style.display="none";
                    }, 3000);
                }
            });
            break;
        case 2:
            $.ajax({
                url: 'index_sendmail.html',
                type:'POST',
                data: {
                    mail:loginName,
                    tempnum:tempnum
                },
                dataType:"json",
                success: function(res){
                    var status = res.status;
                    var tipObj = document.getElementById('sendMailTip');
                    switch(status){
                        case 0:
                            tipObj.innerHTML = '邮件发送成功。';
                            break;
                        case 1:
                            tipObj.innerHTML = '邮件发送失败。';
                            break;
                        case 2:
                            tipObj.innerHTML = '此注册邮箱不存在。';
                            break;
						case 3:
                            tipObj.innerHTML = '邮件发送失败，请重试。';
                            break;
						case 4:
                            tipObj.innerHTML = '请不要反复发送邮件。';
                            break;
                    }
                    tipObj.style.display="block";
                    setTimeout(function(){
                        tipObj.style.display="none";
                    }, 3000);
                }
            });
            break;
    }
}