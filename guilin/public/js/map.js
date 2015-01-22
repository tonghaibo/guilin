var showBmap = {
	obj:'',
	map:'',
	point:new BMap.Point(110.296457,25.279863),
	show:function(){
		this.map = new BMap.Map(this.obj);
		this.map.setMinZoom(12);
		this.map.centerAndZoom(this.point,14);
		//this.map.centerAndZoom("桂林",14);   
		this.map.enableScrollWheelZoom();
		//this.add(this.point.lng,this.point.lat);
		//增加卫星
		this.map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP,BMAP_HYBRID_MAP],anchor: BMAP_ANCHOR_TOP_RIGHT}));
		//缩放
		this.map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_LEFT}));
	},
	add:function(lng,lat){
		//加点
		var myIcon = new BMap.Icon('public/images/admin/marker.png',new BMap.Size(18,28));
		var marker = new BMap.Marker(new BMap.Point(lng,lat),{icon:myIcon});
		marker.setIcon('public/images/admin/marker.gif');
		this.map.addOverlay(marker);
	},
	search:function(obj,name,num){
		//this.map.setZoom(20);
		var options = {
			 onSearchComplete:function(re){
				if(local.getStatus()==BMAP_STATUS_SUCCESS){
					var s = [];
					var count = re.getCurrentNumPois();
					var str = '';
					for(var i=0;i<count;i++){
						str += '<div class="result"><b class="corange">'+(i+1)+'</b>,<a href="javascript:void(0)" onclick="showBmap.move(\''+re.getPoi(i).point.lng+'\',\''+re.getPoi(i).point.lat+'\');'
						str += 'showBmap.label(\''+re.getPoi(i).title+'\',\''+re.getPoi(i).point.lng+'\',\''+re.getPoi(i).point.lat+'\')">';
						str += re.getPoi(i).title+'</a>';
						str += '<span>('+re.getPoi(i).address+')</span>';
						str += '</div>';
					}
					str += '<p>'+(re.getPageIndex()+1)+'/'+re.getNumPages()+'页&nbsp;&nbsp;'+'<a href="javascript:void(0)" onclick="BMap.I(\'TANGRAM__5n\').toPage(8);">上一页</a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="showBmap.search(\''+obj+'\',\'风景\','+(re.getPageIndex()+1)+')" >下一页</a></p>'
					document.getElementById(obj).innerHTML = str;
				}else{
					document.getElementById(obj).innerHTML = '没有搜索到任何数据！';
				}
				$('#bmappanel').removeClass('icon-zan').addClass('icon-sou');
				$('#bmappanel').parent().height(500).width('35%');
			 }
		};
		var local =  new BMap.LocalSearch(this.map,{renderOptions: {map: this.map, panel: "sleft"}});
		//var local =  new BMap.LocalSearch(this.map,options);
		local.search(name);
		$('#bmappanel').removeClass('icon-zan').addClass('icon-sou');
		$('#bmappanel').parent().height(500).width('35%');
	},
	move:function(lng,lat){
		var points = new BMap.Point(lng,lat);
		this.map.panTo(points);
	},
	clear:function(){
		this.map.clearOverlays();
	},
	info:function(lng,lat,id){
		if(id){
		$.get('map_view.html',{'id':id},function(d){
		if(d!=''){
			d = eval('('+d+')');
			if(d.error==0){
				var str = '<div style="margin:0;padding:0;font-size:12px;color:#444;line-height:20px;width:350px"><div style="float:left;width:160px;"><img width="150px" height="95px" src="'+d.data.img+'" />';
				str += '<br><a style="color:blue" href="javascript:void(0)" onclick="showBmap.open('+d.data.id+',\''+d.data.name+'\',\'c\')" />分享</a> <a style="color:blue" href="javascript:void(0)" onclick="showBmap.open('+d.data.id+',\''+d.data.name+'\',\'h\')" />酒店</a> <a style="color:blue" href="javascript:void(0)" onclick="showBmap.open('+d.data.id+',\''+d.data.name+'\',\'w\')" />游玩</a> <a style="color:blue" href="javascript:void(0)" onclick="showBmap.open('+d.data.id+',\''+d.data.name+'\',\'u\')" />TA喜欢</a></div>';
				str += '<div style="float:left;width:190px;height:auto"><a style="color:#DD4B39;font-size:14px;"  target="_parent" href="javascript:void(0)">'+d.data.name+'</a>';
				var l = d.data.des.length;
				if(l>80){
					var s = d.data.des.substr(0,80);
					var t = d.data.des;
					d.data.des = s+' <a href="javascript:void()" onclick="show_content(\''+t+'\',\''+d.data.name+'\')">更多</a>';
				}
				str += '<br>'+d.data.des+'</div>';
				var info = new BMap.InfoWindow(str);
				showBmap.map.openInfoWindow(info,new BMap.Point(lng,lat));
			}else{
				show_msg.open('参数错误！或没有该景点',3,2);
			}
		}
		});
		}
	},
	click:function(e){
		//备用
		$('#lng').val(e.point.lng);
		$('#lat').val(e.point.lat);
		showBmap.getAddress(e.point.lng,e.point.lat);
	},
	label:function(content,lng,lat,id){
		var len = content.length;
		len = len*12+20;
		var content1 = '<div class="marker" style="width:'+len+'px" ids="'+id+'" onmouseover="showBmap.cBack(this);" onmouseout="showBmap.cBacks(this)"><span class="l"></span><span class="w">'+content+'</span><span class="r"></span></div>';
		var label = new BMap.Label(content1);
		label.setStyle({border:'none',padding:'0px',background:'none'});
		label.setPosition(new BMap.Point(lng,lat));
		label.setOffset(new BMap.Size(0,-40));
		this.map.addOverlay(label);
		label.addEventListener('click', function(){
			showBmap.info(lng,lat,id);
		});
	},
	cBack:function(obj){
		$(obj).children().addClass('ckb');
	},
	cBacks:function(obj){
		$(obj).children().removeClass('ckb');
	},
	getAddress:function(lng,lat){
		var gc = new BMap.Geocoder();
		var str = '';
		gc.getLocation(new BMap.Point(lng,lat),function(rs){
			//alert(rs.address);
			rs = rs.addressComponents;
			$('#address').val(rs.district+rs.street+rs.streetNumber);			
		});
	},
	open:function(id,t,type){
		type = type?type:'c';
		var str = '<div class="hd clearfix">';
		str += '<a href="javascript:void(0)" onclick="showBmap.load(this,1)" ids="'+id+'" '+(type=='c'?'class="sel"':'')+' tag="c">分享</a>';
		str += '<a  href="javascript:void(0)" onclick="showBmap.load(this,1)" ids="'+id+'" '+(type=='h'?'class="sel"':'')+' tag="h">酒店</a>';
		str += '<a  href="javascript:void(0)" onclick="showBmap.load(this,1)" ids="'+id+'" '+(type=='w'?'class="sel"':'')+' tag="w">游玩</a>';
		str += '<a  href="javascript:void(0)" onclick="showBmap.load(this,1)" ids="'+id+'" '+(type=='u'?'class="sel"':'')+' tag="u">TA喜欢</a>';
		str += '</div>';
		if(id!=''){
			show_dialog.openHtml('<div class="loadmap">'+str+'<div class="load"><div style="text-align:center;"><img src="public/images/loading.gif" /></div></div></div>',t,600,450);
			showBmap.load('.loadmap .hd a.sel',1);
		}
	},
	load:function(obj,page){
		$(obj).addClass('sel').siblings().removeClass('sel');
		var type = $(obj).attr('tag');
		var id = $(obj).attr('ids');
		$('div.loadmap .load').load('map_load.html?page='+page,{'type':type,'id':id});
	}
};