/*查看更多 JavaScript Document */
function _g(id){return document.getElementById(id)};
function gL(x){var l=0;while(x){l+=x.offsetLeft;x=x.offsetParent;}return l};
function gT(x){var t=0;while(x){t+=x.offsetTop;x=x.offsetParent;}return t};
document.getElementsByClassName=function(tag,cName){var els= [];var myclass=new RegExp("\\b"+cName+"\\b");var elem=this.getElementsByTagName(tag);for(var h=0;h<elem.length;h++){if(myclass.test(elem[h].className))els.push(elem[h])}return els};
function xmlHttp(Url,xmlBack){	var xObj=null;try{xObj=new ActiveXObject("MSXML2.XMLHTTP")}catch(e){try{xObj=new ActiveXObject("Microsoft.XMLHTTP")}catch(e2){try{xObj=new XMLHttpRequest()}catch(e){}}};with(xObj){open("get",Url, true);onreadystatechange=function(){if(readyState==4&&status==200){xmlBack(responseText)}};send(null)}};
function jsLoad(_src,_back){var spt= document.createElement("script");spt.type = "text/javascript";spt.src=_src;document.body.appendChild(spt);spt.onload=spt.onreadystatechange=_back;}


var tabs,dplay;
function vs(n,as){
	if(!as)return;
	if(as.innerHTML=='查看更多房型 ▼'){
		as.innerHTML='关闭更多房型 ▲';
		dplay='';
	}else{
		as.innerHTML='查看更多房型 ▼';
		dplay='none';
	}
	if(!tabs)tabs=document.getElementsByClassName('table','listhidebox');
	var trs=tabs[n-1].getElementsByTagName('tr');
	var n=0;
	for(var i=0;i<trs.length;i++){		
		if(n==0&&trs[i].className=='nodisplay')n=1;
		if(n==1)trs[i].style.display=dplay;
	}	
}

function vsTicket(n,as){
	if(!as)return;
	if(as.innerHTML=='查看更多门票 ▼'){
		as.innerHTML='关闭更多门票 ▲';
		dplay='';
	}else{
		as.innerHTML='查看更多门票 ▼';
		dplay='none';
	}
	if(!tabs)tabs=document.getElementsByClassName('table','listhidebox');
	var trs=tabs[n-1].getElementsByTagName('tr');
	var n=0;
	for(var i=0;i<trs.length;i++){		
		if(n==0&&trs[i].className=='nodisplay')n=1;
		if(n==1)trs[i].style.display=dplay;
	}	
}
function show(strtype){document.getElementById(strtype).style.display="block";document.getElementById("Image_"+strtype).style.display="block";}
function hide(strtype){document.getElementById(strtype).style.display="none";document.getElementById("Image_"+strtype).style.display="none";}


/*查看更多 JavaScript Document  end*/

/*全局的正则表达式*/
 var isEmail = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;//邮编
 var isPhone = /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/;
 var isMobile = /^((\(\d{2,3}\))|(\d{3}\-))?13\d{9}$/;
 var isNumber = /^\d+$/;//数字
 var isInteger = /^[-\+]?\d+$/;
 var isEnglish = /^[A-Za-z]+$/;
/*全局的正则表达式end*/

 try {
     /*去虚线框*/
     $("a").bind("focus", function() {
         if (this.blur) {
             this.blur();
         }
     });
 } catch (e) { }

/*解决Ie背景图片重复加载的BUG*/
document.execCommand("BackgroundImageCache", false, true);

function DoCode(code) {
   if (!!(window.attachEvent && !window.opera)) {
        //ie
        execScript(code);
    } else {
        //not ie
        window.eval(code);
    }
 }