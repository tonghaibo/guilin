function $(objID){return document.getElementById(objID);}
function nav_on(num){if(curtype=$("nav_"+num)){curtype.className='nav_on';if(num!='home'){if($("nav_home")) $("nav_home").className='';}}}
function sel_tag(obj,num,no){if(!no)no=2;var endi=1+no;for(i=1;i<endi;i++){if(i==num){$(obj+'_'+i).className='on';$(obj+'_content_'+i).style.display='block';}else{$(obj+'_'+i).className='off';$(obj+'_content_'+i).style.display='none';}}}
function chk(t){if(t.value=='请输入需要查找的内容'){t.value='';t.style.color='#000000';}}
function chk_so(obj){var tf=$(obj);if(tf.q.value==''||tf.q.value=='请输入需要查找的内容'){alert("请输入关键词");return false;}}
function chk_jd(){var tf=$("jd_so");if(tf.q.value!=''){tf.q.value+=' 景点';}else{alert("请输入要查找的景点名称");return false;}}
function pagecheck(turl,ext){var inputs=document.getElementsByTagName('input');for(var i=1;i<inputs.length;i++){var input=inputs[i];if(input.type=="text"&&input.name=="custompage"&&input.value!=""){window.location=turl+input.value+ext;}}}
function b(s){s=$(s);if(!s){return}
s.onmouseenter=function(){this.className=this.className+' hover';};s.onmouseleave=function(){this.className=this.className.replace(" hover","");}}
function init(){document.soform.q.focus();b("more_service");}
function Report_Error(t,u,p){
	var r = Math.random();
	if(!p) p = document.location.href;
	ur = t>10 ? 'report_info.php' : 'report_error.php';
	window.showModelessDialog("http://www.cncn.com/"+ur+"?t="+t+"&u="+u+"&p="+p+"&r="+r,window,"dialogWidth:400px;dialogHeight:300px;center=1;help=0;status=0;scrollbars=0;resizable=0");
}
var page_start = new Date().getTime();