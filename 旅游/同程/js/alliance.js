(function(){var  m = document.uniqueID && document.compatMode  && !window.XMLHttpRequest && document.execCommand;try{if(!!m){ m("BackgroundImageCache", false, true);}}catch(oh){};})();

var tcGuidec_ClastScrollY=0;
var tcGuidec_ServiceListTop = 30;
var tcGuidec_ServiceListbDraged=false;


function tcGuidec_AutoScroll()
{  var scrollPos;if(typeof window.pageYOffset!="undefined"){scrollPos=window.pageYOffset;}else if(typeof document.compatMode!="undefined"&&document.compatMode!="BackCompat"){scrollPos=document.documentElement.scrollTop;}else if(typeof document.body!="undefined"){scrollPos=document.body.scrollTop;}
if(scrollPos!=tcGuidec_ClastScrollY){var percent=0.1*(scrollPos-tcGuidec_ClastScrollY);if(percent>0){percent=Math.ceil(percent);}else{percent=Math.floor(percent);}
tcGuidec_ClastScrollY=tcGuidec_ClastScrollY+percent;}
if(tcGuidec$("9dcc1d7d067da39f")!=null){if(tcGuidec_ServiceListbDraged==false){tcGuidec$("9dcc1d7d067da39f").style.top=(tcGuidec_ClastScrollY+tcGuidec_ServiceListTop)+"px";}}
if(tcGuidec$('9fc9da9dd0a4aea9')!=null){if(tcGuidec$('9fc9da9dd0a4aea9').style.display==''||tcGuidec$('9fc9da9dd0a4aea9').style.display=='block'){tcGuidec$("9fc9da9dd0a4aea9").style.top=(tcGuidec_ClastScrollY+250)+"px";}}
window.setTimeout("tcGuidec_AutoScroll()",20);}

document.writeln("<script id='f754e648b3673e883494a3876d5a246' type='text/javascript' ><\/script\>");

function guidecFloadJS(){var oScript=null;var method="";var callBack="";var parameter="";var id="f754e648b3673e883494a3876d5a246";var charset="utf-8";var requestUrl;var head=document.getElementsByTagName("head").item(0);var isIE=navigator.appName.indexOf("Microsoft")!=-1?true:false;var readyState=(document.readyState=="complete"||document.readyState=="loaded")?true:false;this.Method=function(mehod){method=mehod;};this.CallBack=function(callback){callBack=callback;};this.Param=function(name,param){parameter+="&"+name+"="+escape(param);};this.Charset=function(charsetvalue){charset=charsetvalue;};this.Request=function(url){requestUrl=url+"?mod="+method+parameter+"&charset="+charset+"&callback="+callBack+"&____v="+Math.random();if(!isIE||readyState){oScript=document.createElement("script");oScript.src=requestUrl;oScript.charset=charset;oScript.type="text/javascript";if(isIE)
{oScript.onreadystatechange=function(){if(!(/loaded|complete/i.test(oScript.readyState)))return;oScript.onreadystatechange=null;oScript.parentNode.removeChild(oScript);oScript=null;};}else{oScript.onload=function(){oScript.parentNode.removeChild(oScript);oScript=null;}}
head.appendChild(oScript);}else{oScript=document.getElementById(id);if(oScript!=null){oScript.type="text/javascript";oScript.charset=charset;oScript.src=requestUrl;}}}}

function tcGuidec_ServiceListDialogDrag(e){var isIE=navigator.appName.indexOf("Microsoft")!=-1?true:false;var evt;if(isIE)evt=window.event;else evt=e;var oDragOrig=tcGuidec$('3dabca25fcbedc73');oDragOrig.style.width=tcGuidec$('9dcc1d7d067da39f').offsetWidth+"px";oDragOrig.style.height=tcGuidec$('9dcc1d7d067da39f').offsetHeight+"px";oDragOrig.style.top=tcGuidec$('9dcc1d7d067da39f').offsetTop+"px";oDragOrig.style.left=tcGuidec$('9dcc1d7d067da39f').offsetLeft+"px";oDragOrig.style.display="block";if(!oDragOrig)return;tcGuidec_ServiceListbDraged=false;oDragOrig.style.cursor="default";var ofs=Offset(oDragOrig);oDragOrig.style.position="absolute";oDragOrig.style.left=ofs.l;oDragOrig.style.top=ofs.t;oDragOrig.X=evt.clientX-ofs.l;oDragOrig.Y=evt.clientY-ofs.t;tcGuidec_ServiceListbDraged=true;if(oDragOrig.addEventListener){oDragOrig.addEventListener('mousemove',moveHandler,true);oDragOrig.addEventListener('mouseup',upHandler,true);}else if(oDragOrig.attachEvent){oDragOrig.attachEvent('onmousemove',moveHandler);oDragOrig.attachEvent('onmouseup',upHandler);}
function moveHandler(evt){if(tcGuidec_ServiceListbDraged==false)return;oDragOrig.style.left=(evt.clientX-oDragOrig.X)+"px";oDragOrig.style.top=(evt.clientY-oDragOrig.Y)+"px";var left=oDragOrig.style.left.replace('px','');if(left<0)
oDragOrig.style.left="0px";var top=oDragOrig.style.top.replace('px','');if(top<0)
oDragOrig.style.top="0px";};function upHandler(){tcGuidec_ServiceListbDraged=false;tcGuidec_ServiceListTop=oDragOrig.offsetTop-tcGuidec_ClastScrollY;tcGuidec$('9dcc1d7d067da39f').style.top=oDragOrig.style.top;tcGuidec$('9dcc1d7d067da39f').style.left=oDragOrig.style.left;oDragOrig.style.display="none";window.focus();if(oDragOrig.removeEventListener){oDragOrig.removeEventListener('mousemove',moveHandler,true);oDragOrig.removeEventListener('mouseup',upHandler,true);}else if(oDragOrig.detachEvent){oDragOrig.releaseCapture();oDragOrig.detachEvent('onmousemove',moveHandler);oDragOrig.detachEvent('onmouseup',upHandler);}
oDragOrig.style.cursor="default";};function Offset(e){var t=e.offsetTop;var l=e.offsetLeft;var w=e.offsetWidth;var h=e.offsetHeight;while(e=e.offsetParent){t+=e.offsetTop;l+=e.offsetLeft;}
return{t:t,l:l,w:w,h:h}};}

var tcGuidec$=function(id){return document.getElementById(id);};

function tcGuidec_loadflash(file,w,h){if(navigator.appName.indexOf("Microsoft")!=-1){document.write('<object id="tc_netSiteGuidec_as_local" name="tc_netSiteGuidec_as_local" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+w+'" height="'+h+'">');document.write('<param name="movie" value="'+file+'" />');}else{document.write('<object id="tc_netSiteGuidec_as_local" name="tc_netSiteGuidec_as_local" type="application/x-shockwave-flash" data="'+file+'" width="'+w+'" height="'+h+'">');}
document.write('<param name="allowScriptAccess" value="always" />');document.write('</object>');}

function tcGuidec_getFlashObject(movieName)
{if(window.document[movieName])
{return window.document[movieName];}
if(navigator.appName.indexOf("Microsoft Internet")==-1)
{if(document.embeds&&document.embeds[movieName])
return document.embeds[movieName];}
else
{return document.getElementById(movieName);}}
function tcGuidec_SetCookie(name,value,timer)
{var expires=";";if(timer>0){var date=new Date();date.setTime(date.getTime()+timer);expires+="expires="+date.toGMTString();}
document.cookie=name+"="+escape(value)+expires+"; path=/";}
function tcGuidec_GetCookie(name)
{var nameEQ=name+"=";var ca=document.cookie.split(";");for(var i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==" ")c=c.substring(1,c.length);if(c.indexOf(nameEQ)==0)return unescape(c.substring(nameEQ.length,c.length));}
return null;}


var tcGuidec_CheckFlash_Count=0;
function tcGuidec_CheckLocationConfig()
{
   if(tcGuidec_CheckFlash_Count > 20){
      tcGuidec_VisitorId = tcGuidec_GetCookie("tc_netSiteGuidec_VisitorId");
      tcGuidec_AllianceConfig();
     return ;
   }
   tcGuidec_CheckFlash_Count ++;
    try
     {
         var v = tcGuidec_getFlashObject("tc_netSiteGuidec_as_local").read();
       
         if(v == null){
            tcGuidec_VisitorId = tcGuidec_GetCookie("tc_netSiteGuidec_VisitorId");
         }else{
           tcGuidec_VisitorId = v;
         }
         tcGuidec_AllianceConfig();       
     }
    catch(e)
     {
        window.setTimeout("tcGuidec_CheckLocationConfig()",100);
     }
}

function tcGuidecCloseServiceList(){tcGuidec$('9dcc1d7d067da39f').style.display="none";}
function tcGuidecMaxMinServiceList(){if(tcGuidec$('76b99aa1e3934dba').style.display==''||tcGuidec$('76b99aa1e3934dba').style.display=='block')
tcGuidec$('76b99aa1e3934dba').style.display="none";else
tcGuidec$('76b99aa1e3934dba').style.display="block";}

 /*
  * gn 组名集合
  * gc 组数量集合
  * nl 客服名称集合
  * al 客服账号集合
  * sl 性别集合
  * ol 在线状态集合
  */
  
 function tcGuidecCreateServiceList$(groupName,gl,gn){   
          var fstyle = "b";
          
          var f= [];    
          f.push("<table style='table-layout:fixed; width:100%; font-size:12px;border-spacing:0px;' cellpadding='0' cellspacing='0' >");
          f.push("<tr><td  style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_topimg.gif) no-repeat;height:4px; width:140px;'></td></tr>");
          f.push("<tr>");
          f.push("<td>");
          f.push("<table style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_toptit.gif) no-repeat;width:140px;border-spacing:0px; height:22px; color:#373737; font-size:12px;'cellpadding='0' cellspacing='0'>");
          f.push("<tr>");
          f.push("<td style='width:100px; padding-left:10px; text-align:left;' onmousedown='tcGuidec_ServiceListDialogDrag(event);'>E通天下</td>");
          f.push("<td style='width:20px;'align='left'><span style='display:-moz-inline-box; display:inline-block; width:12px; background:url("+ tcGuidecImageUrl$ +"images/guidecbuttons.gif) -82px 0px; cursor:pointer;' onclick='tcGuidecMaxMinServiceList()' title='最小化'>&nbsp;</span></td>");
          f.push("<td style='width:20px;'align='left'><span style='display:-moz-inline-box; display:inline-block; width:12px; background:url("+ tcGuidecImageUrl$ +"images/guidecbuttons.gif) -100px 0px;cursor:pointer;' onclick='tcGuidecCloseServiceList()' title='关闭'>&nbsp;</span></td>");
          f.push("</td>");
          f.push("</tr>");
          f.push("</table>");
          f.push("</td>");
          f.push("</tr>");
          f.push("</table>");      
          f.push("<table id='76b99aa1e3934dba' style='table-layout:fixed; width:100%; font-size:12px; display:block; border-spacing:0px;' cellpadding='0' cellspacing='0' >");
          f.push("<tr><td style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_img.gif) no-repeat;  height:37px; '></td></tr>");     
          
          f.push("<tr>");
          f.push("<td style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_subtit.gif) no-repeat;  height:23px ;color:#373737; font-size:11px; text-align:center;'>");
          f.push(groupName);
          f.push("</td>");
          f.push("</tr>");  
         
          var index= 0;
          for(var i=0; i < gl.length; i++ )
          {
            f.push("<tr> <td style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_numbg.gif) repeat-y; height:5px'></td> </tr>");
            f.push("<tr style='cursor:pointer'  title='"+ gn[i] +"'> ");
            f.push("<td style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_numbg.gif) repeat-y'>");
            f.push("<table style='table-layout:fixed; border-spacing:0px;'  cellpadding='0' cellspacing='0'  border-spacing='0'>");
            f.push("<tr onclick=\"tcGuidec_OpenDialogueBox('" + gl[i] + "',0)\" >");
            f.push("<td  style='width:30px'  align='right'> <img   src='"+ tcGuidecImageUrl$ +"images/man01.gif' /> </td> ");
            
            f.push("<td style='width:60px; font-size:12px;' align='center'  >"+ gn[i] +"</td> ");
            f.push("<td style='width:50px; font-size:12px ;color:#999999;' align='left' title='在线'>[<font style='color:#FE3000' >在线</font>]</td> ");
            f.push("</tr> ");
            f.push("</table>");
            f.push("</td>");
            f.push("</tr>");
          }   
           
        f.push("<tr> <td style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_numbg.gif) repeat-y; height:3px'></td> </tr>");
        f.push("</table>");
        f.push("<table style='table-layout:fixed; width:100%; font-size:12px; border-spacing:0px;' cellpadding='0' cellspacing='0' >");
        

        f.push("<tr> <td style='background:url("+ tcGuidecImageUrl$ +"images/"+ fstyle +"_bot.gif) no-repeat; height:4px '></td></tr>"); 
        f.push("</table>");          
        tcGuidec$('9dcc1d7d067da39f').innerHTML = f.join('');
        tcGuidec$('9dcc1d7d067da39f').style.display ="block";
  }
  
var tcGuidec_DialogueBox=null;
function tcGuidec_OpenDialogueBox(guidec,invite){
 if(document.all){
   window.open(tcGuidec_ServerUrl$ + "Chat.aspx?VId="+tcGuidec_VisitorId+"&GId="+guidec  ,"e通天下","left=200,top=10,width=730,height=600,toolbar=no,menubar=no,scrollbars=no,resizable=yes,location=no,status=no");
 }else{
    if(tcGuidec_DialogueBox==null || tcGuidec_DialogueBox.closed){
         tcGuidec_DialogueBox = window.open(tcGuidec_ServerUrl$ + "Chat.aspx?VId="+tcGuidec_VisitorId+"&GId="+guidec  ,"e通天下","left=200,top=10,width=730,height=600,toolbar=no,menubar=no,scrollbars=no,resizable=yes,location=no,status=no");
     }else{
       try{
            tcGuidec_DialogueBox.focus();
          }catch(e){
             tcGuidec_DialogueBox = window.open(tcGuidec_ServerUrl$ +"Chat.aspx?VId="+tcGuidec_VisitorId+"&GId="+guidec  ,"e通天下","left=200,top=10,width=730,height=600,toolbar=no,menubar=no,scrollbars=no,resizable=yes,location=no,status=no");
          }
     }
   }
}; 

 var tcGuidec_RequestNetSiteConfigCheckedAjax = null;
 function tcGuidec_AllianceConfig(){
      if(parseInt(tcGuidec_VisitorId) == 0){
         tcGuidec_CreateNumber();
      }
 
      var referer = document.referer;
       if(referer ==null || referer =="")referer="";
      var ajax = new guidecFloadJS();
          ajax.Method("GetAllianceConfig");
          ajax.Param("VisitorId",tcGuidec_VisitorId);
          ajax.Charset(tcGuidec$Charset);
          ajax.CallBack("tcGuidec_AllianceConfigCallBack$");
          ajax.Request( tcGuidec_ServerUrl$ +"AllianceGuidec.aspx");      
  }
  
 function tcGuidec_AllianceConfigCallBack$(o){
    if(o.results && o.results.group && o.results.gn && o.results.gl){
        tcGuidecCreateServiceList$(o.results.group,o.results.gl,o.results.gn);
        tcGuidec_AutoScroll(); 
    }
 }
 
 function tcGuidec_CreateNumber(){
   var ajax = new guidecFloadJS();
       ajax.Method("CreateGuidecNumber");
       ajax.Param("VisitorId",tcGuidec_VisitorId);
       ajax.Charset(tcGuidec$Charset);
       ajax.CallBack("tcGuidec_CreateNumberCallBack$");
       ajax.Request( tcGuidec_ServerUrl$ +"AllianceGuidec.aspx");      
 }
  
  function tcGuidec_CreateNumberCallBack$(o){
    if(o.results && o.results.vi){
        if(parseInt(o.results.vi) >0){
          tcGuidec_VisitorId = o.results.vi;
        }
    }
  }

(function(){
         var t = [];
          t.push("<style type='text/css'>");
          t.push(".tcGuidecfilter{ border: solid 1px #999999; position:absolute; z-index:202000; top:0px;left:0px; background-color:#F0F0F0; filter:alpha(opacity=60); -moz-opacity:.60;opacity:0.6}");
          t.push("</style>");
          t.push("<div style='display:none;' class='tcGuidecfilter' id='3dabca25fcbedc73'></div>");
          t.push("<div id='9dcc1d7d067da39f' style='width:140px; height:auto; font-size:12px;  position:absolute; right:5px ;top:5px; z-index:100000; display:none;' ></div>");   
          document.write(t.join(''));      
          tcGuidec_loadflash(tcGuidec_ServerUrl$ + "shareObject.swf",0,0);
          tcGuidec_CheckLocationConfig();
})();

 
  


