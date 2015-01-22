lastScrollY=0;
function heartBeat(){ 
var diffY;
if (document.documentElement && document.documentElement.scrollTop)
    diffY = document.documentElement.scrollTop;
else if (document.body)
    diffY = document.body.scrollTop
else
    {/*Netscape stuff*/}
percent=.1*(diffY-lastScrollY); 
if(percent>0)percent=Math.ceil(percent); 
else percent=Math.floor(percent); 
document.getElementById("full").style.top=parseInt(document.getElementById("full").style.top)+percent+"px";
lastScrollY=lastScrollY+percent; 
}
var url="/OnlineService/Talk.html";
suspendcode="<div id=\"full\" style='right:5px;POSITION:absolute;TOP:350px;z-index:100' class='S_index_CustomerService'><a href='##' onclick=\"javascript:window.open ('"+url+"','newwindow','height=550,width=730,top=200,left=200,toolbar=no,menubar=no,scrollbars=no');\"></a></div>"
document.write(suspendcode);
window.setInterval("heartBeat()",1); 