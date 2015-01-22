

if(typeof(menu_num)!='undefined'){
for(var i=1;i<=menu_num;i++){
	if($('left_menu_'+i)){
		$('left_menu_'+i).className = "";
	}
}
}
if(typeof(left_id)!='undefined'){
    $('left_menu_'+left_id).className = "on";
}	
	  	
if($("tab")){

    var Ptr=$("tab").getElementsByTagName("dl");
    if(Ptr){
        for (i=1;i<=Ptr.length;i++) {
              Ptr[i-1].className = (i%2>0)?"t1":"t2";
        }
        
        for(var i=0;i<Ptr.length;i++) {
              Ptr[i].onmouseover=function(){
              this.tmpClass=this.className;
              this.className = "t3";   
              };
              Ptr[i].onmouseout=function(){
              this.className=this.tmpClass;
              };
        }
    }
    
    
    var Ptr=$("tab").getElementsByTagName("tr");
    if(Ptr){
        for (i=1;i<=Ptr.length;i++) {
              Ptr[i-1].className = (i%2>0)?"t1":"t2";
        }
        for(var i=0;i<Ptr.length;i++) {
              Ptr[i].onmouseover=function(){
              this.tmpClass=this.className;
              this.className = "t3";   
              };
              Ptr[i].onmouseout=function(){
              this.className=this.tmpClass;
              };
        }
    }

}