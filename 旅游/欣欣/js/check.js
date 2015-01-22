function $(objID) {
	return document.getElementById(objID);
}
//if(top.location == self.location){
//  top.location = "/member/?from="+escape(self.location);
//}

	function copy_to_clip(objid){
		var obj = window.clipboardData;
  	var str = document.getElementById(objid).innerText;
  	//window.top.parent.location.href;
		obj.setData('Text',str);
		alert('你已经成功复制本段内容！');
	}
	
	
    function goto(page)
    {
        document.form1.topage.value=page;
        document.form1.submit();
    }
    
  function is_checkbox(){
        var Is=false;
        for(var i=0;i<document.form1.length;i++)
        {
            var box=document.form1.elements[i];
            if (box.name.substr(0,9)=='checkboxs' && box.checked==true)
           {
                Is=true;
                break;
            }
        }
        return Is;
  }
  
    function batch_del()
    {
        if (is_checkbox())
        {
            if ( window.confirm('您确定要删除所选信息吗？') )
            {
                    document.form1.todo.value="del";
                    document.form1.submit();
            }
        }
        else
        {
            alert("对不起，您还未选择要删除的信息！");
        }
    }
    function Selectall()
    {
         for(var i=0;i<document.form1.length;i++)
         {
            if (document.form1.elements[i].value== "all" )
            {
            	var c =document.form1.elements[i];
               	break;
            }
         }

         for(var i=0;i<document.form1.length;i++)
         {
               var box=document.form1.elements[i]
               if ((box.name.substr(0,9)=='checkboxs') && (box.checked != c.checked))
               	  box.click();
         }

    }
    
