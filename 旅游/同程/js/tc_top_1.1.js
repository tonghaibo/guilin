
/** SQUL (Simple Quick Unitive Light)
 * @author kares,牛仔,蔡仔,陈仔
 * @version 2011-04-12 alpha-1.0.6
 * @modify 2011-06-23 蔡天旭
 * @modify 2011-07-21 蔡天旭
 * @modify 2011-08-24 蔡天旭 
 * @modify 2011-08-30 蔡天旭 
 */
(function(window){

//一个沙盒
(function(){
    //root核心拥有的高权限对象
    var superUser = {};
    //级联核心数组
    var levelCore = [];
	
    var squl, 
		//级联核心数
		levelNum = 10, 
		//执行单元的名称，用于debug级核心
		execUnitList = 	"on setStyle getStyle setClass addClass removeClass " +
    					"bubbleOn anim";
		//execUnitList = "";
		
    					
						
    //核心级的清单，序号越高，则在原型链的越外层
    var levelList = {
		//debug核心
		debug : 0,
		//基本核心
		base : 1, 
        //执行单元的级别
        exec : 2,
        //桥接单元
        bri : 3,
        //组件核心的级别
        comp : 4,
        //默认绑定到squl的核心级别,一般用于挂载分支处理的对象等！！！
        admin : 6,
        //最高权限的核心
        root : 9
    };
    
    
    /** 使用原型链接来创建新对象
     * @param {Object} obj 要赋予原型的对象
     */
    var protoTo = function(obj){
        var Func = function(){};
        Func.prototype = obj;
        return new Func();
    };//var protoTo - END

    /** 扩展对象，仅适用在单层的扩展中
     * @param {Object} merge 来源对象
     * @param {Object} tar 扩展的目标对象
     */
    var extend = function(merge, tar){
        if (merge != null && tar != null) {
            var src, copy, name;
            for (name in merge) {
                if (merge.hasOwnProperty(name)) {
                    copy = merge[name];
                    if (tar === copy) {
                        continue;
                    };
                    if (copy !== undefined) {
                        tar[name] = copy;
						//TODO：把扩展的这个属性名加到debug核心中
                        if(tar !== levelCore[levelList.debug]){
                        	superUser.addPreventUnit(name);                        	
                        }
                    };
                };//if-END
            };//for-END
            return tar;
        }//if-END
    };


    /**
     * 创建级联核心数组
     * @param {Number} num 级联数
     */
    superUser.linkLevelCore = function(num){
        var nextCore;
		levelCore = [];
        levelNum = num;
        for (var n = 0; n < num; n++) {
            nextCore = 	levelCore[n - 1] ?
						levelCore[n - 1] : {};
            levelCore[n] = protoTo(nextCore);
            levelCore[n].level = n;
			levelCore[n].squl = "1.0";
        }
    };
    
    /**
     * 在底层核心加载所有接口对象的函数，当上层核心的某一对象没有具体实现时，会进入底层核心的debug实现。
     * @param {String} objList 用空格来分隔每个对象的名字
     */
    superUser.addPreventUnit = function(objList, callBack){
    	if(	objList != null &&
    		objList !== ""){
	        execUnitList = objList;
	        var objArray = objList.split(" ");
			var debugObj = {};
	        for (var nNum = 0; nNum < objArray.length; nNum++) {
				//立即执行来捕获闭包变量
	            debugObj[objArray[nNum]] = function(callName, callBack){
	                //debug实现函数体
	                return function(){
	                    //callName为被调用的函数
						//TODO：callback没有实现
						if (callBack) {
							callBack({
								fun: callName,
								param: arguments
							});
						}
	                    //do something...
	                    return squl;
	                };
	            }(objArray[nNum], callBack);
	        }
			extend(debugObj, levelCore[levelList.debug]);

    	}
    };
    
    //创建级联核心
	superUser.linkLevelCore(levelNum);
    //添加debug单元
	superUser.addPreventUnit(execUnitList);
	
	
	/**
	 * 选择核心的函数
	 * @param {Object} levelIndex
	 * @return {SQUL} 某一级别的核心
	 */
	var core = function(levelIndex){
        if (typeof levelIndex === "number" &&
			levelIndex >= 0 &&
			levelIndex < levelNum) {
            return levelCore[levelIndex];
        }
		else if(	typeof levelIndex === "string" &&
					levelList[levelIndex] != null){
            return levelCore[levelList[levelIndex]];
		}
	}
	

	
	
    //扩展一些基本处理函数到底层
    extend({
        /**
         * 选择执行的核心级
         * @param {Number} levelIndex 核心序号
         */
        use : function(levelIndex, callBack){
            if (typeof levelIndex === "number" &&
				levelIndex >= 0 &&
				levelIndex <= levelNum-1) {
				if(callBack){
					callBack(levelCore[levelIndex]);
				}
				else{
                	squl = levelCore[index];
				}
            }
			else if(	typeof levelIndex === "string" &&
						levelList[levelIndex] != null){
				if(callBack){
					callBack(levelCore[levelList[levelIndex]]);
				}
				else{
                	squl = levelCore[levelList[levelIndex]];
				}          
			}
        },
        /**
         * 扩展组件
         * @param {String} levelName
         * @param {Object} componentObj
         */
        add : function(levelName, componentObj){
            if (typeof levelName === "string" &&
				levelList[levelName] != null &&
				levelList[levelName] <= this.level) {
                extend(componentObj, levelCore[levelList[levelName]]);
            }
			else if(typeof levelName === "object"){
				extend(levelName, this);
			}
        }
    }, levelCore[levelList.base]);
	
    //把superUser扩展到核心级中
    core("root").add(superUser);
	
    squl = core("admin");
    window.squl = squl;
})();


squl.use("exec", function(squl){
	
	/** 扩展对象，仅适用在单层的扩展中
	 * @param {Object} merge 来源对象
	 * @param {Object} tar 扩展的目标对象
	 * @param {Boolean} safe 是否进行安全的扩展，只扩展目标对象中已有的属性
	 */
	var extend = function(merge, tar, safe){
		var already;
		if(!safe){
			already = function(){return true;};
		}
		else{
			already = function(obj, proper){
				return obj.hasOwnProperty(proper);
			}
		}
	    if (merge != null && tar != null) {
	        var src, copy, name;
	        for (name in merge) {
	            if (merge.hasOwnProperty(name)) {
	                copy = merge[name];
	                if (tar === copy) {
	                    continue;
	                };
					//只覆盖已定义的属性？
	                if (copy !== undefined && already(tar, name)) {
	                    tar[name] = copy;
	                };
	            };//if-END
	        };//for-END
	        return tar;
	    }//if-END
	};
	
    /** 使用原型链接来创建新对象
     * @param {Object} obj 要赋予原型的对象
     */
    var pro = function(obj){
        var Func = function(){};
        Func.prototype = obj;
        return new Func();
    };//var pro - END
	
	
    squl.add({
        lang: {
            pro: pro,
            extend: extend
        }
    });
});


/**
 * 
 * @param {Object} listObj
 */
var listToArray = function(listObj){
	var length = listObj.length,
		returnArray = [];
	for(var n=0; n<length; n++){
		returnArray[n] = listObj[n];
	}
	return returnArray;
};

//在squl中使用thunder时引用的一个变量
var domSelector;


(function(){
	
	//一些用于query的正则
	var rwhite = /\s/,
		rClass = /[\n\t]/g,
		rSpaces = /\s+/,
		//去除左空白字符
		trimLeft = /^\s+/,
		trimRight = /\s+$/;

	// 测试能不能索引到\s
	// (IE会索引失败)
	if ( !rwhite.test( "\xA0" ) ) {
		trimLeft = /^[\s\xA0]+/;
		trimRight = /[\s\xA0]+$/;
	}

	// 如果有的话就使用原生的 String.trim 
	var trim = String.prototype.trim ?
		function( text ) {
			return text == null ? "" : String.prototype.trim.call( text );
		} :
		// 没有的话用我们自己的
		function( text ) {
			return text == null ?
				"" :
				text.toString().replace( trimLeft, "" ).replace( trimRight, "" );
		};


	
	
//快速索引(thunder)---------------------
var thunder = {
	/**
	 * classname检测
	 * @param {Object} elem 要检测的DOM元素
	 * @param {Object} classname class属性值
	 */
	checkClass : function(elem, classname){
	    if (elem.className) {
			var test = " " + elem.className + " ";
			if(test.replace(/[\t\n]/g, " ").indexOf(" "+ classname+ " ") >= 0){
				return true;
			}
	    }
	},
	
	/**
	 * 查找一个父级元素下某一classname的元素
	 * @param {Object} classname 类名
	 * @param {Object} domObj 父级DOM元素
	 */
	getChildByClass : function(classname, domObj){
		var allChild = domObj.getElementsByTagName("*");
		var length = allChild.length,
			allFound = [];
		for(var n=0; n<length; n++){
			if(thunder.checkClass(allChild[n], classname)){
				allFound.push(allChild[n]);
			}
		}
		return allFound;
	},
	
	/** 
	 * 根据一个索引字符串来检索出 DOM
	 * @param {String} match
	 * @param {DOM|Array} domLevel
	 * @return {Array} 包含索引到的DOM对象
	 */
	searchLevel : function(match, domLevel){
		
		if(match.indexOf("#") >= 0){
			return thunder.searchUnit.id(match.replace("#", ""));
		}
		else if(match.indexOf(".") >= 0){
			if(match.indexOf(".") === 0){
				return thunder.searchUnit.className(match.replace(".", ""), domLevel);
			}
			else{
				var subTag = match.split(".");
				var subDomLevel = thunder.searchUnit.tag(subTag[0], domLevel);
				return thunder.searchUnit.className(subTag[1], subDomLevel, true);
			}
		}
		else if(match.indexOf(">") >= 0){
			return thunder.searchUnit.child(match, domLevel);
		}
		else{
			return thunder.searchUnit.tag(match, domLevel);
		}
	},
	
	
	
	/**
	 * DOM检索,目前只支持如: "#top", "#top li", "#top .show", "#top .show li", "#top li .show" , "#top li.show""
	 * 或者直接传入DOM对象！！！
	 * @param {Object} query
	 */
	find : function(query, content){
		//字符串搜索
		if(typeof query === "string"){
			if(typeof content === "string"){
				return searchCore(query, searchCore(content));
			}
			return searchCore(query, content);
		}
		else if(query && query.nodeType || query === window || query === document.body){
		//DOM节点或window或document.body
			return [query];
		}
		else if(query && query.length !== 0){
			var returnArray = [];
			for(var n=0; n<query.length; n++){
				if(query[n].nodeType === 1){
					returnArray.push(query[n]);
				}
			}
			return returnArray;
		}
		else{
			return [];
		}

	},
	
	//搜索单元
	searchUnit : {
		
		id : function(match){
			var returnElem =  document.getElementById(match);
			return returnElem ? [returnElem] : [];
		},
		
		tag : function(match, domLevel){
			var length = domLevel.length,
				hasFound = [],
				allFound = [],
				foundRoll;
				
			for(var n=0; n<length; n++){
				foundRoll = listToArray(domLevel[n].getElementsByTagName(match));
				if(foundRoll.length){
					hasFound.push(foundRoll);
				}
			}
			//一次合并所有数组
			allFound = allFound.concat.apply(allFound, hasFound);
			return allFound;
		},
		
		child : function(match, domLevel){
			
		},
		
		className : function(match, domLevel, inThisLevel){
			var length = domLevel.length,
				hasFound = [],
				allFound = [];
			
			if(inThisLevel){
				for(var n=0; n<length; n++){
					if( thunder.checkClass(domLevel[n], match) ){
						allFound.push(domLevel[n]);
					}
				}
			}
			else{
				for(var n=0; n<length; n++){
					foundRoll = thunder.getChildByClass(match, domLevel[n]);
					if(foundRoll.length){
						hasFound.push(foundRoll);
					}
				}
				//一次合并所有数组
				allFound = allFound.concat.apply(allFound, hasFound);		
			}
			return allFound;
		}
	}
}

var searchCore,
	testDiv = document.createElement("div"),
	domLevel;

if(document.querySelectorAll){
	if(testDiv.querySelectorAll){
		searchCore = function(query, content){
			if(!content){
				return document.querySelectorAll(query);			
			}
			else if(content.length){
				var hasFound = [],
					allFound = [];
				
				
				for(var n=0; n<content.length; n++){
					foundRoll = content[n].querySelectorAll(query);
					if(foundRoll.length){
						hasFound.push(listToArray(foundRoll));
					}
				}
				//一次合并所有数组
				allFound = allFound.concat.apply(allFound, hasFound);

				return allFound;
			}
			else if(content.nodeType === 1){
				return content.querySelectorAll(query);
			}
			else {
				return [];
			}
		};
	}
	else{
		searchCore = function(query, content){
			return document.querySelectorAll(query);
		};
	}
}
else{
	searchCore = function(query, content){
		var splitArray = trim(query).split(","),
			arrayFound = [],
			subSplitUnit,
			subLength,
			subDomLevel,
			allFound = [];
			
			domLevel = content ? content : document;
			domLevel = domLevel.length != null ? domLevel : [domLevel];
			
			for(var n=0;n<splitArray.length;n++){
				subDomLevel = domLevel;
				subSplitUnit = trim(splitArray[n]).split(" ");
				subLength = subSplitUnit.length;
				for(var i=0; i<subLength; i++){
					subDomLevel = thunder.searchLevel(subSplitUnit[i], subDomLevel);
				}
				arrayFound[n] = subDomLevel;
			}
			//一次合并所有数组
			allFound = allFound.concat.apply(allFound, arrayFound);
			
		return allFound;
	};
}



//引用到外部
domSelector = thunder;

})();

  
//添加必要的执行对象
squl.add("exec", {
	/**
	 * DOM检索,目前只支持如: "#top", "#top li", "#top .show", "#top .show li", "#top li .show"
	 * @param {Object|String} query 检索字符串或DOM本身
	 * @return {Object} squl检索对象
	 */
	dom : function(query, content){
		var squlObj;
	
		
		var SqulConstructer = function(){
			var allFound,
				length;
				//使用thunder索引
			allFound = domSelector.find(query, content);
			length = allFound.length;
			for(var n=0; n<length; n++){
				this[n] = allFound[n];
			}
			this.length = length;
			return this;
		};
		//this 为调用这个函数的squl核心级
		SqulConstructer.prototype = this;
		
		squlObj = new SqulConstructer();
				
		return squlObj;
	},
	
	/**
	 * 添加子节点
	 * @param {Squl|String|DOM} arguments 多个参数会依次执行添加
	 */
	appendChild : function(){
		for(var nNum=0; nNum<arguments.length; nNum++){
			//如果是HTML字符串
			if(typeof arguments[nNum] === "string"){
				var elemGhost;
				//TODO:这里有很多问题
				if(arguments[nNum].indexOf("option") > -1 ){
					elemGhost = document.createElement("select");
				}
				else{
					elemGhost = document.createElement("table");
				}
				elemGhost.innerHTML = arguments[nNum];
				var childLength = elemGhost.childNodes.length;
				this.each(function(){
					for(var i=childLength-1; i>=0; i--){
						this.appendChild(elemGhost.childNodes[i]);
					}
				});
			}
			//如果是DOM文档节点
			else if(arguments[nNum].nodeType === 1){
				var elem = arguments[nNum];
				this.each(function(){
					this.appendChild(elem);
				});
			}
			//如果是squl对象
			//TODO:有点怪异的执行方式
			else if(arguments[nNum].squl){
				var elem = arguments[nNum];
				this.each(function(){
					var that = this;
					elem.each(function(){
						that.appendChild(this);
					});
				});
			}
		}
	},
	
	/**
	 * 对每个索引到的DOM执行一个callBack5
	 * @param {arguments} 按类型来区分
	 */
	each : function(){
		//TODO:添加对字符串each执行的支持
		if(typeof arguments[0] === "function"){
			var length = this.length;
			for (var n = 0; n < length; n++) {
				arguments[0].call(this[n], n);
			}
		}
		else if(typeof arguments[0] === "string" && typeof arguments[1] === "function"){
			
		}
		else if(typeof arguments[0] === "object" && typeof arguments[1] === "function"){
			if(arguments[0].length != null && arguments[0][0] != null){
				for(var n = 0; n < arguments[0].length; n++) {
					arguments[1](arguments[0][n], n);
				}		
			}
		}

	}
});
	
	
   
    
})(window);
//----------------------------tc_admin_0.1--------------------------------//
squl.use("admin", function(squl){
    squl.add({
        create: function(type, state){
            if (squl["create" + type]) {
                return squl["create" + type](state);
            }
        }// create-END
    });
    
});

//----------------------------tc_admin_0.1--------------------------------//

//日历控件闭包
squl.use("comp", function(squl)
{
	var CTX = {};
    var clickTime = null;
	/**
	 * 创建日期控件
	 */
    function createDateTime(createDate,pClass,txtTime,endTime,starTime,funObj,isover)
    {        
		if (document.getElementById(createDate+"DateTime")) 
		{ 
			document.getElementById(createDate+"DateTime").style.display = "inline";
            
            if (endTime && starTime) 
            {
                var lastMonth = document.getElementById(createDate + "lastSpan");
                var nextMonth = document.getElementById(createDate + "nextSpan");
                if (lastMonth) 
                {
                    lastMonth.onclick = function()
                    {
						var nowDate = document.getElementById(createDate+"nowTime").innerHTML;
						var year =  nowDate.substring(0,4);
						var month = nowDate.substring(nowDate.indexOf('年')+1,nowDate.indexOf('月'));
						month = parseInt(month,10)-1;
						if(month<1)
						{
							month = 12;
							year = parseInt(year)-1;
						}
						getTime(createDate, year+'-'+ month, txtTime, endTime,starTime,funObj,isover);                        
                    }
                }
                if (nextMonth) 
                {
                    nextMonth.onclick = function()
                    {
						var nowDate = document.getElementById(createDate+"nowTime").innerHTML;
						var year =  nowDate.substring(0,4);
						var month = nowDate.substring(nowDate.indexOf('年')+1,nowDate.indexOf('月'));
						month = parseInt(month,10)+1;
						if(month>12)
						{
							month = 1;
							year = parseInt(year)+ 1;
						}
						getTime(createDate, year+'-'+ month, txtTime, endTime,starTime,funObj,isover);                         
                    }
                }
            }

		}
		else 
		{

			var iframe = document.createElement("iframe");
			iframe.className = "if";
			
			var DateTime1 = document.createElement("div");
			DateTime1.id = createDate+"DateTime";
			DateTime1.style.display = "inline";
			DateTime1.onselectstart = function(){return false;}
			//获取控件添加容器
			var createDateTime = document.getElementById(createDate);
			
			var DateTime = document.createElement("div");
			DateTime.className = pClass;
			
			
			//创建头部
			var top = document.createElement("div");
			top.className = "top";
			
			var lastMonth = document.createElement("span");
			lastMonth.className = "lastMonth";
			lastMonth.id= createDate+"lastSpan";
			lastMonth.onclick = function()
			{
                var nowDate = document.getElementById(createDate + "nowTime").innerHTML;
                var year = nowDate.substring(0, 4);
                var month = nowDate.substring(nowDate.indexOf('年') + 1, nowDate.indexOf('月'));
                month = parseInt(month,10) - 1;
                if (month < 1) 
                {
                    month = 12;
                    year = parseInt(year) - 1;
                }
                getTime(createDate, year + '-' + month, txtTime, endTime, starTime, funObj,isover);		
			}
			
			var lastText = document.createElement("h4");
			lastText.className = "lastText";
			lastText.id = createDate+"nowTime";
			lastText.innerHTML = "xxxx年x月";
			
			var nextMonth = document.createElement("span");
			nextMonth.className = "nextMonth";
			nextMonth.id= createDate+"nextSpan";
			nextMonth.onclick = function()
			{
                var nowDate = document.getElementById(createDate + "nowTime").innerHTML;
                var year = nowDate.substring(0, 4);
                var month = nowDate.substring(nowDate.indexOf('年') + 1, nowDate.indexOf('月'));
                month = parseInt(month,10) + 1;
                if (month > 12) 
                {
                    month = 1;
                    year = parseInt(year) + 1;
                }
                getTime(createDate, year + '-' + month, txtTime, endTime, starTime, funObj,isover);  			
			}
			
			var nextText = document.createElement("h4");
			nextText.className = "nextText";
			nextText.id = createDate+"nextTime";
			nextText.innerHTML = "xxxx年x月";
			
			top.appendChild(lastMonth);
			top.appendChild(lastText);
			top.appendChild(nextMonth);
			top.appendChild(nextText);
			//添加头部
			DateTime.appendChild(top);
			
			//创建第一个月时间容器
			var conNow = document.createElement("div");
			conNow.className = "contentTime1";
			conNow.id = createDate+"conNow";
			
			var table = document.createElement("table");
			table.id = createDate+"nowMonth";
			table.cellPadding = "0";
			table.cellSpacing = "0";
			var tbody = document.createElement("tbody");
			table.appendChild(tbody);
			
			var tr = document.createElement("tr");
			var th;
			
			for (var j = 1; j <= 7; j++) 
			{
				th = document.createElement("th");
				switch (j)
				{
					case 1:
						th.innerHTML = "日";
						break;
					case 2:
						th.innerHTML = "一";
						break;
					case 3:
						th.innerHTML = "二";
						break;
					case 4:
						th.innerHTML = "三";
						break;
					case 5:
						th.innerHTML = "四";
						break;
					case 6:
						th.innerHTML = "五";
						break;
					case 7:
						th.innerHTML = "六";
						break;
				}
				tr.appendChild(th);
			}
			tbody.appendChild(tr);
			conNow.appendChild(table);
			
			//创建第二个月时间容器
			var conNext = document.createElement("div");
			conNext.className = "contentTime2";
			conNext.id = createDate+"conNext";
			
			var table1 = document.createElement("table");
			table1.id = createDate+"nextMonth";
			table1.cellPadding = "0";
			table1.cellSpacing = "0";
			var tbody1 = document.createElement("tbody");
			table1.appendChild(tbody1);
			
			var tr1 = document.createElement("tr");
			var th1;
			
			for (var j = 1; j <= 7; j++) 
			{
				th1 = document.createElement("th");
				switch (j)
				{
					case 1:
						th1.innerHTML = "日";
						break;
					case 2:
						th1.innerHTML = "一";
						break;
					case 3:
						th1.innerHTML = "二";
						break;
					case 4:
						th1.innerHTML = "三";
						break;
					case 5:
						th1.innerHTML = "四";
						break;
					case 6:
						th1.innerHTML = "五";
						break;
					case 7:
						th1.innerHTML = "六";
						break;
				}
				tr1.appendChild(th1);
			}
			tbody1.appendChild(tr1);
			conNext.appendChild(table1);
			
			
			
			DateTime.appendChild(conNow);
			DateTime.appendChild(conNext);
			DateTime1.appendChild(iframe);
			DateTime1.appendChild(DateTime);
			createDateTime.appendChild(DateTime1);
		}
    };//createTime-end
    
	/**
	 * 改变日期
	 * @param {Object} num 月份变换数目
	 */
    function getTime(createDateTime,nowDate,txtTime,endTime,starTime,funObj,isover)
    {
        var date = new Date();        
        var year = date.getFullYear();
		var month = date.getMonth()+1;
		if(nowDate.length>1)
		{
            var nowTime = nowDate.split('-');
            year = nowTime[0];
            month = nowTime[1];
		}
		else
		{            
			var now = document.getElementById(txtTime).value;
            if (now) 
            {
              if (now.indexOf("时间") == -1) 
			  {
			  	var nowTime = now.split('-');
			  	year = nowTime[0];
			  	month = nowTime[1];
			  }
            }
		}
		      
        
        //设置月份变换
        getDays(year + '-' + (month) + '-' + 1, createDateTime+'nowMonth', createDateTime+'nowTime', createDateTime+"conNow",txtTime,createDateTime,endTime,starTime,funObj,isover);
        getDays(year + '-' + (parseInt(month,10) + 1) + '-' + 1, createDateTime+'nextMonth', createDateTime+'nextTime', createDateTime+"conNext",txtTime,createDateTime,endTime,starTime,funObj,isover);
    };//getTime-end
    
    
    /**
     * 生成一个月数据
     * @param {Object} dateTime
     */
    function getDays(dateTime, table, nowTime, content,txtTime,createDate,endTime,starTime,funObj,isover)
    {
        var upTime = dateTime.split('-');
		var up_year = parseInt(upTime[0],10);
		var up_month = parseInt(upTime[1],10);
		var isOverTime = isover;
		if(up_month>12)
		{
			up_year++;
			up_month = 1;
		}
		else if(up_month<1)
		{
			up_year--;
			up_month = 12;
		}
		dateTime = up_year +'-'+ up_month +'-'+ 1;
		
		var date = new Date(Date.parse(dateTime.replace(/-/g, '/')));
		
        // 获取是星期几
        var nowday = date.getDay();
        // 获取当前月份 
        var curMonth = date.getMonth();
        // 生成实际的月份: 由于curMonth会比实际月份小1, 故需加1 */
        date.setMonth(curMonth + 1);
        // 将日期设置为0
        date.setDate(0);
        // 返回当月的天数			   
        var allDayNum = date.getDate(0);
        //计算控件头部信息
        var nowTimeTxT = document.getElementById(nowTime);
        
        if (nowTimeTxT != null) 
        {
            nowTimeTxT.innerHTML = date.getFullYear() + "年" + (date.getMonth() + 1) + "月";
        }
        
        //设置该月份 背景图片
        //document.getElementById(content).style.background = "url(http://css.17u.cn/comm/images/cn/public/month" + (date.getMonth() + 1) + ".png)";
        
        
        
        var table = document.getElementById(table);
        //添加之前删除表格元素
        while (table.rows.length > 1) 
        {
            table.deleteRow(1);
        }
        
        var newTr;
        var newTdNum = allDayNum;
        if (nowday != 7) 
        {
            newTdNum = newTdNum + nowday;
        }
        for (var i = 1; i <= 42; i++) 
        {
            if (i == 1 || i == 8 || i == 15 || i == 22 || i == 29 || i == 36) 
            {
                //添加一行
                newTr = table.insertRow(table.rows.length);
                
            }
            if (nowday != 7 && i <= nowday) 
            {
                //添加本月1号前的空白列
                var newTd0 = newTr.insertCell((i % 7) - 1);
                //设置列内容和属性
                newTd0.innerHTML = "  ";
                newTd0.className = "td02";
                var span = document.createElement("span");
                span.innerHTML = " ";
                span.className = "spanOut";
                newTd0.appendChild(span);
                continue;
            }
            else if (i > newTdNum) 
            {
                //添加本月最后一天的空白列
                var newTd0 = newTr.insertCell((i % 7) - 1);
                //设置列内容和属性
                newTd0.innerHTML = "  ";
                newTd0.className = "td02";
                var span = document.createElement("span");
                span.innerHTML = " ";
                span.className = "spanOut";
                newTd0.appendChild(span);
                continue;
            }
            else 
            {
                //添加列
                var newTd0 = newTr.insertCell((i % 7) - 1);
                //设置列内容和属性
                //newTd0.innerHTML = i-nowday;
                newTd0.className = "td01";
                var span = document.createElement("span");
                span.innerHTML = i - nowday;
                span.className = "spanOut";
                
                span.onmouseover = function()
                {
                    this.className = "spanHover";
                };
                span.onmouseout = function()
                {
                    this.className = "spanOut";
                };
                
                //获取今天日期给予特殊样式
                var addrt = false;
				var addOverTime = false;
                var nowdate = new Date();
                
                if (nowdate.getFullYear() > date.getFullYear()) 
                {
                    addrt = true;
                }
                else if (nowdate.getFullYear() == date.getFullYear()) 
                {
                    if ((nowdate.getMonth() + 1) > (date.getMonth() + 1)) 
                    {
                        addrt = true;
                    }
                    else if ((nowdate.getMonth() + 1) == (date.getMonth() + 1)) 
                    {
                        if (nowdate.getDate() > (i - nowday)) 
                        {
                            addrt = true;
                        }
                    }
                }
				//结束日期
				if(endTime)				
				{
					var endDate = endTime.split('-');
					if(endDate.length>0)
					{
						if(parseInt(endDate[0])<date.getFullYear())
						{
							addrt = true;
						}
						else if(parseInt(endDate[0]) == date.getFullYear())
						{
							if(parseInt(endDate[1],10)<(date.getMonth() + 1))
							{
								addrt = true;
							}
							else if(parseInt(endDate[1],10) == (date.getMonth() + 1))
							{
								if(parseInt(endDate[2],10) < (i - nowday))
								{
									addrt = true;
								}
							}
						}
					}
				}
				//开始日期
				if(starTime)
				{
					var startDate = starTime.split('-');
					if(startDate.length>0)
					{
						if(parseInt(startDate[0])>date.getFullYear())
						{
							addrt = true;
						}
						else if(parseInt(startDate[0])==date.getFullYear())
						{
							if(parseInt(startDate[1],10)>(date.getMonth() + 1))
							{
								addrt = true;
							}
							else
							{
								if(parseInt(startDate[1],10)==(date.getMonth() + 1))
								{
									if(parseInt(startDate[2],10) >= (i - nowday))
									{
										addrt = true;
									}
								}
							}
						}
					}
				}
				
                //绑定今天样式
                if (addrt && isOverTime) 
                {
                    span.className = "spanOver";
                    span.onmouseover = function()
                    {
                        this.className = "spanOver";
                    };
                    span.onmouseout = function()
                    {
                        this.className = "spanOver";
                    };
                }
                else 
                {
                    span.onclick = function()
                    {
                        
                        clickTime = date.getFullYear();
						if((date.getMonth() + 1)<10)
						{
							clickTime = clickTime + "-0" + (date.getMonth() + 1);
						}
						else
						{
							clickTime = clickTime + "-" + (date.getMonth() + 1);
						}
						if(parseInt(this.innerHTML)<10)
						{
							clickTime = clickTime + "-0" + this.innerHTML;
						}
						else
						{
							clickTime = clickTime + "-" + this.innerHTML;
						}
                        var setTxtTime = document.getElementById(txtTime);
						//设置选择的日期
                        if (setTxtTime) 
                        {
                            setTxtTime.value = clickTime;
                        }                    
                        document.getElementById(createDate+"DateTime").style.display = "none";
						//判断是否有事件绑定
						if(funObj)
						{
							funObj(clickTime);
						}
                    };
                }
                //绑定今天样式
                if (date.getFullYear() == nowdate.getFullYear() && (date.getMonth() + 1) == (nowdate.getMonth() + 1) && (i - nowday) == nowdate.getDate()) 
                {
                    span.className = "spanDay";
                    span.onmouseout = function()
                    {
                        this.className = "spanDay";
                    };
                }
				//绑定选择过的日期
                if(clickTime!= null)
				{
					var cliyear = clickTime.substring(0,clickTime.indexOf('-'));
					var cliMonth = clickTime.substring(clickTime.indexOf('-')+1,clickTime.lastIndexOf('-'));
					var cliDay = clickTime.substring(clickTime.lastIndexOf('-')+1,clickTime.length);
					if(date.getMonth()+1 == parseInt(cliMonth,10) && i-nowday == parseInt(cliDay,10) && date.getFullYear() == parseInt(cliyear))
					{
						span.className = "clickDate";
						span.onmouseout = function()
                        {
                            this.className = "clickDate";
                        };
					}
				}
                
				
                newTd0.appendChild(span);
            }
        };
        
        }//getdays-end
		
				
        
        CTX.createDateTime = function(param)
        {
            var DateTime = document.getElementById(param.append+"DateTime");	
			var isover = true;
			if(param.isOverTime)
			{
				isover = false;
			}		
            if (DateTime) 
            {
                if (DateTime.style.display == "inline" || DateTime.style.display == "block" || DateTime.style.display == "") 
                {
                    DateTime.style.display = "none";
					flip = 1;
                }
                else 
                {
                    //调用生成     
                    createDateTime(param.append, param.pClass,param.txtTime,param.endTime,param.starTime,param.funObj,isover);
                    getTime(param.append,0,param.txtTime,param.endTime,param.starTime,param.funObj,isover);										
                }
            }
            else 
            {
                //调用生成     
                createDateTime(param.append, param.pClass,param.txtTime,param.endTime,param.starTime,param.funObj,isover);
                getTime(param.append,0, param.txtTime,param.endTime,param.starTime,param.funObj,isover);	
            }     
		};//createDateTime-end  starTime

	
	
	squl.add("comp", CTX);
});



//----------------------------tc_comp_0.1--------------------------------//
squl.use("comp", function(squl){
    var CTX = {};

    /**
     * 选项卡
     * @param {Object} param.actType:鼠标事件
     * @param {String} param.parent:父控件ID
     * @param {String} param.execElem:控件类型 如：li
     * @param {String} param.cParent:父控件ID
     * @param {String} param.showElem:控件类型 如：div
     * @param {String} param.dfClass:标题默认样式
     * @param {String} param.newClass:标题改变样式
     * @param {String} param.cdfClass:内容默认样式
     * @param {String} param.cnewClass:内容改变样式
     */
    CTX.createTab = function(param){
        var UL = document.getElementById(param.parent);
        var UL_LIST1 = UL.getElementsByTagName(param.execElem);
		var UL_LIST = new Array();
		for(var i=0;i<UL_LIST1.length;i++)
		{
			if(UL_LIST1[i].parentNode.id == param.parent)
			{
				UL_LIST[i] = UL_LIST1[i];
			}
		}
		
		
        var DIV = document.getElementById(param.cParent);
        var DIV_LIST = new Array();
        if (DIV) {
            var DIV_LIST1 = DIV.getElementsByTagName(param.showElem);
			
			for(var i=0;i<DIV_LIST1.length;i++)
			{
				if(DIV_LIST1[i].parentNode.id == param.cParent)
				{
					var DIVNODES = DIV_LIST1[i];
					
					DIV_LIST[DIV_LIST.length] = DIVNODES;
					
				}
			}
        }
        
        
        for (var i = 0; i < UL_LIST.length; i++) {
            squl.on(param.actType, function(){
                //调用内部组件修改操作控件样式为默认值 
                if (param.dfClass) {
                    squl.updateClass(param.dfClass, "#" + param.parent + " " + param.execElem,param.parent);
                }
                if (param.newClass) {
                    this.className = param.newClass;
                }
                if (param.cParent) {
                    //调用内部组件修改显示控件样式为默认值
                    squl.updateClass(param.cdfClass, "#" + param.cParent + " " + param.showElem,param.cParent);
                    //判断处理如果父控件类型与子控件类型的问题
                    if (DIV_LIST.length === UL_LIST.length) {
                        for (v = 0; v < UL_LIST.length; v++) {
                            if (UL_LIST[v].id === this.id) {
                                squl.setClass(param.cnewClass, "#" + DIV_LIST[v].id);								
                            }
                        };//for-end
                    }
                    else {
                        for (v = 0; v < UL_LIST.length; v++) {
                            if (UL_LIST[v].id === this.id) {
                                document.getElementById(DIV_LIST[v].id).className = param.cnewClass;
                            }
                        };//for-end
                    };//else - End
	            };
	    	}, UL_LIST[i]);
        };//for-END
    };
	
     
	/**
	 * 弹出框
	 * @param {String} param.actType:触发事件
	 * @param {String} param.triggerCol:触发控件
	 * @param {String} param.upCol:弹出控件
	 */
	CTX.createUpWindow = function(param)
	{		
		var obj = document.getElementById(param.upCol);
		if(param.actType === "mouseover")		
		{
			squl.on("mouseover", function(){
          		  obj.style.display = "block";
       		}, "#" + param.triggerCol);
			squl.on("mouseout", function(){
          		   obj.style.display = "none";
       		}, "#" + param.triggerCol);
		}
		else
		{	
			squl.on(param.actType, function(){
          		  squl.show("#" + param.upCol);
       		}, "#" + param.triggerCol);
		}
	};//UpWindow - end
	
	/**
	 * 弹出层
	 * @param {Object} param
	 */
	CTX.createUpTier = function (param)
	{
		var Sys = {}; //存放所有浏览器类型
		var ua = navigator.userAgent.toLowerCase(); //获取浏览器信息
		
		try 
		{
			if (!jQuery) 
			{ return; }
			
			jQuery(document).ready(function()
			{
				if (param.conClass == null) 
				{
					var defaultParam = 
					{
						inline: true,
						href: "#" + param.upCol,
						onCleanup: param.onClose,
						overlayClose: true,
						escKey: true
					};
					param.upCol = param.onClose = null;
					param.escKey = param.overlayClose;
					squl.lang.extend(param, defaultParam);
					
					var paramInner = defaultParam;					
					jQuery.colorbox(paramInner);
				}
				else 
				{
					jQuery("." + param.conClass).colorbox(
					{
						inline: true,
						href: "#" + param.upCol
					});
				}
				
				//处理关闭按钮
				if (param.exitList) 
				{
					var exitList = param.exitList.split(",");
					for (var i = 0; i < exitList.length; i++) 
					{
						squl.on("click", function()
						{
							jQuery.colorbox.close();
						}, "#" + exitList[i]);
					}
				}
			});
		} 
		catch (e) 
		{
			return;
		}
	}	
    squl.add("comp", CTX);    
});
//----------------------------tc_comp_0.1--------------------------------//

//----------------------------tc_bri_0.1--------------------------------//
squl.use("bri", function(squl){
    
});
//----------------------------tc_bri_0.1--------------------------------//

//----------------------------tc_exec_0.1--------------------------------//
squl.use("exec", function(squl){
    squl.add({
		//统一的事件对象
        eventUtil: {
            /** 监听事件添加
             * @param {DOM} obj 要监听的DOM元素
             * @param {String} type 监听类型
             * @param {Function} fn 事件触发时执行的函数
             */
            add: function(obj, type, fn){
                if (obj) {
                    if (obj.addEventListener) {
                        obj.addEventListener(type, fn, false);
                    }
                    else if (obj.attachEvent) {
                        var funIe = function(){
                            fn.call(obj);
                        };
                        obj.attachEvent("on" + type, funIe);
                    }
					else{
						obj["on" + type] = fn;
					}
                };
            },
			
			/**
			 * 去除事件监听
			 * @param {Object} obj
			 * @param {Object} type
			 * @param {Object} fn
			 */
            remove: function(obj, type, fn){
				if(obj){
	                if (obj.removeEventListener) {
	                    obj.removeEventListener(type, fn, false);
	                }
	                else if (obj.detachEvent) {
	                    obj.detachEvent("on" + type, fn);
	                }
	                else {
	                    obj["on" + type] = null;
	                }
				}
            },
            /**
             * 获取event对象
             * @param {Object} event 触发的event对象
             */
            getEvent: function(event){
                return event ? event : window.event;
            },
            /**
             * 获取event的底层目标
             * @param {Object} event 触发的event对象
             */
            getTarget: function(event)
			{
				var e = this.getEvent(event);
                return e.target || e.srcElement;
            },
            /**
             * 屏蔽event的默认行为
             * @param {Object} event 触发的event对象
             */
            preventDefault: function(event){
				var e = this.getEvent(event);
                if (e.preventDefault) {
                    e.preventDefault();
                }
                else {
                    e.returnValue = false;
                }
            },
			
			/**
			 * 阻止事件冒泡
			 */
            stopBubble: function(event){
				var e = this.getEvent(event);
                if (e.stopPropagation) {
                    e.stopPropagation();
                }
                else {
                    e.cancelBubble = true;
                }
            },
			
			
            getRelated: function(event){
				var e = this.getEvent(event);
                if (e.relatedTarget) {
                    return e.relatedTarget;
                }
                else if (e.toElement) {
                    return e.toElement;
                }
                else if (e.fromElement) {
                    return e.fromElement;
                }
                else {
                    return null;
                }
            }
        }//eventUtil - END
    });
    
    
    var tcExec = {};
    /** 
     * 鼠标事件绑定
     * @param {String} type 事件类型，exp:"click", "over"
     * @param {Object} param 事件触发时的响应
     * @param {String} domQuery DOM元素的查询字符串，目前只支持ID
     */
    tcExec.on = function(type, param, domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) {
	        squl.eventUtil.add(execElems[n], type, param);
		}
    };//on-END
    
    tcExec.remove = function(type, param, domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) {
	        squl.eventUtil.remove(execElems[n], type, param);
		}
    };//on-END
    
    /**
     * 显示活隐藏
     * @param {String} domQuery
     */
    tcExec.show = function(domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) {
	        if (execElems[n]) {
	            if (execElems[n].style.display === "block") {
	                execElems[n].style.display = "none";
	            }
	            else {
	                execElems[n].style.display = "block";
	            }
	        }
		}
    }
	
    /** 直接改变CSS属性值
     * @param {Object} param 要改变的属性和值 exp: height:"300px", top:"50px"
     * @param {String} domQuery 传入查询字符串,目前只支持id exp: "#top"
     */
    tcExec.setStyle = function(param, domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) {
            for (name in param) {
                if (execElems[n].style[name] !== undefined) {
                    execElems[n].style[name] = param[name];
                }
            }
		}
    };//setStyle-end
    
    /** 直接改变CSS
     * @param {Object} cName 样式名称
     * @param {String} domQuery 控件ID
     */
    tcExec.setClass = function(cName, domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) {
			execElems[n].className = cName;
		}
    };//setClass-end
    
	
	/** 直接改变CSS
     * @param {Object} cName 样式名称
     * @param {String} domQuery 控件ID
     */
    tcExec.updateClass = function(cName, domQuery,cParent)
	{
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) 
		{
			if(execElems[n].parentNode.id == cParent)
			{
				execElems[n].className = cName;
			}
			//execElems[n].className = cName;
		}
    };//setClass-end
    
	
	/** 改变当前控件文本
     * @param {String} domQuery 控件ID
     */
    tcExec.setValue= function(txtValue,domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) {			
			execElems[n].value = txtValue;    
		}
    };//setTime-end
    
	
	
	
	
	//一些用于query的正则
	var rwhite = /\s/,
		rClass = /[\n\t]/g,
		rSpaces = /\s+/,
		//去除左空白字符
		trimLeft = /^\s+/,
		trimRight = /\s+$/;

	// 测试能不能索引到\s
	// (IE会索引失败)
	if ( !rwhite.test( "\xA0" ) ) {
		trimLeft = /^[\s\xA0]+/;
		trimRight = /[\s\xA0]+$/;
	}

	// 如果有的话就使用原生的 String.trim 
	var trim = String.prototype.trim ?
		function( text ) {
			return text == null ? "" : String.prototype.trim.call( text );
		} :
		// 没有的话用我们自己的
		function( text ) {
			return text == null ?
				"" :
				text.toString().replace( trimLeft, "" ).replace( trimRight, "" );
		};

	
	
	/** add class
	 * @param {String} value
	 */
	tcExec.addClass = function( value, domQuery ) {
		if ( value && typeof value === "string" ) {
			var classNames = (value || "").split( rSpaces ),
				elem;
			
			var execElems = domQuery != undefined ? this.dom(domQuery) : this,
				length = execElems.length;
			for (var n = 0; n < length; n++) {
				elem = execElems[n];
				if (elem.nodeType === 1) {
					if (!elem.className) {
						elem.className = value;
					}
					else {
						var className = " " + elem.className + " ",
							setClass = elem.className;
						
						for (var c = 0, cl = classNames.length; c < cl; c++) {
							if (className.indexOf(" " + classNames[c] + " ") < 0) {
								setClass += " " + classNames[c];
							}
						}
						elem.className = trim(setClass);
					}
				}
			}//for
		}
		//返回squl的实例
		return this;
	};
	
	/** hello world
	 * @param {String} value
	 */
	tcExec.removeClass = function( value, domQuery ) {
		if (value && typeof value === "string") {
			var classNames = (value || "").split( rSpaces ),
				elem;
			
			var execElems = domQuery != undefined ? this.dom(domQuery) : this,
				length = execElems.length;
			for (var n = 0; n < length; n++) {
				elem = execElems[n];
				if (elem.nodeType === 1 && elem.className) {
					var className = (" " + elem.className + " ").replace(rClass, " ");
					for (var c = 0, cl = classNames.length; c < cl; c++) {
						className = className.replace(" " + classNames[c] + " ", " ");
					}
					elem.className = trim(className);
				}
			}//for
		}
		return this;
	};

    
	tcExec.set = function(type, value, domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		for (var n = 0; n < length; n++) {
			execElems[n][type] = value;
		}
	};
	
	
	tcExec.get = function(type, domQuery){
		var execElems = domQuery != undefined ? this.dom(domQuery) : this,
			length = execElems.length;
		var returnValue = [];
		for (var n = 0; n < length; n++) {
			returnValue[n] = execElems[n][type];
		}
		return returnValue;
	};
	/**
	 * jsonp 请求
	 * @param {Object} url
	 */
	tcExec.ajaxJsonp = function(url)
	{
		var scr = document.createElement("script");
		scr.type = "text/javascript";
		scr.src = url;
		document.getElementsByTagName('head')[0].appendChild(scr);				
	}
	/**
	 * 跨域请求
	 * @param {Object} url
	 * @param {Object} funobj
	 */
	tcExec.crossAjax = function(url,funobj)
	{
		var scr = document.createElement("script");
		scr.type = "text/javascript";
		var js_url = url.replace("&","|^|");
		while(js_url.indexOf("&") != -1)
		{
			js_url = js_url.replace("&","|^|");
		}
		scr.src = "http://172.16.2.34:199/HttpHandlers/CN_Ajaxprot.ashx?jurl="+encodeURIComponent(js_url)+"&JSONP=TCCNAJAX_JSONP";
		document.getElementsByTagName('head')[0].appendChild(scr);
		window.TCCNAJAX_JSONP = function(value)
		{
			funobj(value);
		}		
	}
	tcExec.domCoord = function(obj)
	{
		var param = {height:parseInt(obj.clientHeight),width:parseInt(obj.clientWidth),top:0,left:0};
		do 
		{
			param.top += obj.offsetTop || 0;
			param.left += obj.offsetLeft || 0;
			obj = obj.offsetParent;
		}
		while (obj);
		
		return param;
	}
	tcExec.webShare = function(param)
	{		
		if (param.isCss && param.isCss == false) 
		{			
			//不加载样式
		}
		else
		{
			var css = document.createElement("link");
			css.href = "http://js.40017.cn/cn/comm/script/2011/bar.css?v=" + Math.random();
			css.rel = "stylesheet";
			css.type = "text/css";
			document.getElementsByTagName("body")[0].appendChild(css);
		}
		
		squl.ajaxJsonp("http://js.40017.cn/cn/comm/script/2011/Squl_fxList_Content.js?v=" + Math.random());		
		
		var pdiv  = document.getElementById(param.showDom);
			pdiv.onmouseover = function(e)
			{
				var obj = document.getElementById("SQULFXINFO");				
				obj.style.display = "inline";
				var tl = squl.domCoord(this);
				obj.style.top = tl.top + tl.height + "px";
				obj.style.left = tl.left + "px"; 				
			}
			pdiv.onmouseout = function()
			{
				document.getElementById("SQULFXINFO").style.display = "none";
			}		
	}
	/**
	 * 获取当前时间
	 */
	 tcExec.getTime = function(param)
	 {
	 	
		var myDate = new Date();
	 	
	 	if (param) 
		{//带参日期修改	
			//是否时间对比
			if (param.thanTime) 
			{
				var time = Date.parse(param.time.replace(/-/g, '/'));
				var thanTime = Date.parse(param.thanTime.replace(/-/g, '/'));
				
				if (time < thanTime) 
				{ 
					return -1; }
				else if (time == thanTime) 
				{ 
					return 0; }
				else 
				{ 
					return 1; }
				
			}
			
			if (param.time) 
			{
				myDate = new Date(Date.parse(param.time.replace(/-/g, '/')));
			}
			//年份
			if (param.year) 
			{
				myDate.setFullYear(param.year + myDate.getFullYear());
			}
			//月份
			if (param.month) 
			{
				myDate.setMonth(param.month + myDate.getMonth());
			}
			//日期改变
			if (param.date) 
			{
				myDate.setDate(param.date + myDate.getDate());
			}
			//小时改变
			if (param.hours) 
			{
				myDate.setHours(param.hours + myDate.getHours());
			}
			//分钟改变
			if (param.minute) 
			{
				myDate.setMinutes(param.minute + myDate.getMinutes());
			}
			//秒数改变
			if (param.second) 
			{
				myDate.setSeconds(param.second + myDate.getSeconds());
			}
			
		}
			var year = myDate.getFullYear();
			var month = myDate.getMonth() + 1;
			var dateNow = myDate.getDate();
			var hours = myDate.getHours();
			var minute = myDate.getMinutes();
			var second = myDate.getSeconds();
			var unDate = year + "-" + month + "-" + dateNow;
			
			if (param) 
			{
				if (param.timeMode) 
				{
					unDate = "";
					if (param.timeMode.indexOf("YYYY") != -1) 
					{
						unDate = year+"";
					}
					if (param.timeMode.indexOf("MM") != -1) 
					{
						if (unDate == "") 
						{
							unDate = month+"";
						}
						else 
						{
							unDate += "-" + month;
						}
					}
					if (param.timeMode.indexOf("DD") != -1) 
					{
						if (unDate == "") 
						{
							unDate = dateNow+"";
						}
						else 
						{
							unDate += "-" + dateNow;
						}
					}
					if (param.timeMode.indexOf("hh") != -1) 
					{
						if (unDate == "") 
						{
							unDate = hours+"";
						}
						else 
						{
							unDate += " " + hours;
						}
					}
					if (param.timeMode.indexOf("mm") != -1) 
					{
						if (unDate == "") 
						{
							unDate = minute+"";
						}
						else 
						{
							unDate += ":" + minute;
						}
					}
					if (param.timeMode.indexOf("ss") != -1) 
					{
						if (unDate == "") 
						{
							unDate = second+"";
						}
						else 
						{
							unDate += ":" + second;
						}
					}
					if (unDate == "") 
					{
						unDate = year + "-" + month + "-" + dateNow;
					}
				}
			}
				var splitime = unDate.split("-");
				if (splitime.length == 3) 
				{
					var tonewDate = splitime[0];
					if (parseInt(splitime[1], 10) < 10) 
					{
						tonewDate += "-0" + splitime[1];
					}
					else 
					{
						tonewDate += "-" + splitime[1];
					}
					if (parseInt(splitime[2], 10) < 10) 
					{
						tonewDate += "-0" + splitime[2];
					}
					else 
					{
						tonewDate += "-" + splitime[2];
					}
					unDate = tonewDate;
				}		
			return unDate;
		};//getTime-end
	
	/**
	 * 控件值能输入数字
	 * @param {Object} e
	 */
	 tcExec.inputNaN = function(e)
	 {
	    var Sys = {}; //存放所有浏览器类型
        var ua = navigator.userAgent.toLowerCase(); //获取浏览器信息
        var keycode; //键盘输入数据
        //IE浏览器
        if (window.ActiveXObject) 
	    {
	         keycode = event.keyCode;
　　         if(keycode>=48 && keycode<=57 || keycode == 8 || keycode == 13 || keycode == 0)
　　         {
　　            event.returnValue=true;
　　         }
　　         else
　　         {
　　            event.returnValue=false;
　　         }
	    }
	    else
	    {
	        keycode = e.which;
            if(keycode>=48 && keycode<=57 || keycode == 8 || keycode == 13 || keycode == 0)
　　         {
　　            e.returnValue=true;
　　         }
　　         else
　　         {
　　            e.preventDefault();
　　         }
	    };//if else -end
	    var b = squl.eventUtil.getTarget(squl.eventUtil.getEvent(e));
	    b.style.imeMode="disabled";
		b.oncontextmenu = function(){return false;}
		b.onselectstart = function(){return false;}
	 };//inputNaN-end
	 /**
	  * 验证手机号码格式
	  * @param {Object} phone 
	  */
	 tcExec.ValidaPhone = function(phone)
	 {
	 	if(!isNaN(phone))
		{
			if(phone.length == 11)
			{
                var s = phone.substring(0, 2);                
                if (s == "13" || s == "14" || s == "15"|| s == "18") 
                { return true; }
                else 
                { return false; }			
			}else
			{
				return false;
			}
		}
		else		
		{
			return false;
		}
	 };//ValidaPhone-end
	 
	 /**
	  * 邮箱验证
	  * @param {Object} email
	  */
	 tcExec.ValidaEmail = function(email)	 
	 {
		var  s =  /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/;
		if(s.test(email))
		{
			return true;
		}else
		{
			return false;
		}
	 };//email-end
	 /**
	  * 身份证验证15位或18位
	  */
	 tcExec.ValidaIdentity = function(identity){
		 if(identity.length == 15 || identity.length == 18){
			 return true;
		 }
		 else{
			 return false;
		 }
	 };//identity-end
	 /**
	  * 禁止使用
	  * @param {Object} obj
	  */
	tcExec.banOperate  = function(obj)
	{
        
		document.getElementById(obj).onkeydown = function(e)
		{
            var keycode;
            if (window.ActiveXObject) 
            {
                event.returnValue = false;
            }
            else 
            {
                e.preventDefault();
            }
		}
		document.getElementById(obj).style.imeMode="disabled";
		document.getElementById(obj).oncontextmenu = function(){return false;}
		document.getElementById(obj).onselectstart = function(){return false;}
	} 
	tcExec.loseOperate  = function(obj)
	{
        
		document.getElementById(obj).onkeydown = function(e)
		{
           
		}
		document.getElementById(obj).style.imeMode="auto";
		document.getElementById(obj).oncontextmenu = function(){return true;}
		document.getElementById(obj).onselectstart = function(){return true;}
	}
	tcExec.ValidaClick	= function (e,DateTimeid,openDateTimeid)	
	{
        if (openDateTimeid) 
		{
			var a = document.getElementById(DateTimeid);
			if (a) 
			{
				if (a.style.display == "" || a.style.display == "inline" || a.style.display == "block") 
				{
					var b = squl.eventUtil.getTarget(squl.eventUtil.getEvent(e));
					rt = false;
					
					if (b.id == DateTimeid || b.id == openDateTimeid) 
					{
						rt = true;
					}
					else 
					{
						while (b && b.localName != "body") 
						{
							b = b.parentNode;
							if (b != null) 
							{
								if (b.id == DateTimeid || b.id == openDateTimeid) 
								{
									rt = true;
									break;
								}
							}
						}
					}
					
					if (!rt) 
					{
						a.style.display = "none";
					}
				}
			}
		}
		else
		{
			  squl.ValidaClick1(e, DateTimeid);
		}
    };//ValidaClick - end
    tcExec.ValidaClick1	= function (e,pram)	
	{
        var a = document.getElementById(pram[0]);
        if (a) 
        {
            if (a.style.display == "" || a.style.display == "inline" || a.style.display == "block") 
            {
                var b = squl.eventUtil.getTarget(squl.eventUtil.getEvent(e));
                rt = false;                
                
				for (var i = 0; i < pram.length; i++) 				
				{
					if (b.id == pram[i]) 
	                {
	                    rt = true;
	                }
				}                
                if(!rt)
                {
                    while (b && b.localName != "body") 
                    {
                        b = b.parentNode;
                        if (b != null) 
                        {
                            for (var i = 0; i < pram.length; i++) 
                            {
                                if (b.id == pram[i]) 
                                {
                                    rt = true;
									break;
                                }
                            }
                        }
                    }
                }
                
                if (!rt) 
                {
                    a.style.display = "none";
                }                
            }
        }
    };//ValidaClick - end
    
	
//<<--------AjaxObj-------------
	/**
	 * 创建XHR对象
	 */
    function createXHR(){
        if (typeof XMLHttpRequest != "undefined") {
            return new XMLHttpRequest();
        }
        else if (typeof ActiveXObject != "undefined") {
            if (typeof arguments.callee.activeXString != "string") {
                var versions = ["MSXML2.XMLHttp.6.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp"];
                for (var i = 0, len = versions.length; i < len; i++) {
                    try {
                        var xhr = new ActiveXObject(versions[i]);
                        arguments.callee.activeXString = versions[i];
                        return xhr;
                    } 
                    catch (ex) {}
                }
            }
            return new ActiveXObject(arguments.callee.activeXString);
        }
        else {}
    }
	/**
	 * @param {Boolean} delay 是否延时发送请求
	 * @param {Number} timeout 超时时间
	 * @param {String} type	 请求类型 exp:"get", "post"
	 * @param {String} url	地址
	 * @param {String} data 发送的数据 
	 * @param {String} dataType 返回的数据类型 exp:"json", "string"
	 * @param {Boolean} cache get请求是否缓存（在get时添加随即参数）
	 * @param {Function} onTimeout 超时回调	
	 * @param {Function} onFinish 正常结束的回调
	 * @param {Function} onError 出错的回调，在onSendError, onDataError, onTimeout时会同时触发
	 * @param {Function} onSendError 发送出错的回调
	 * @param {Function} onDataSuccess 数据返回并且格式正确的回调
	 * @param {Function} onDataError 返回数据但格式出错的回调（一般是在json parse出错时）
	 * @param {Object} requestHeader 自定义的报头
	 * 
	 */
	var defaultParam = {
		delay:false, 
		timeout : 8000, 
		type:"get", 
		url:"", 
		data:"", 
		dataType:"json", 
		cache:false, 
		onTimeout : function(){}, 
		onFinish : function(){}, 
		onError : function(){}, 
		onSendError : function(){}, 
		onDataSuccess : function(){}, 
		onDataError : function(){}, 
		requestHeader : {  
			"Content-Type":"application/x-www-form-urlencoded"
		}
	}
	
	function setHeader(obj, state){
		var header = state.requestHeader;
		for(name in header){
			if(header.hasOwnProperty(name)){
				obj.setRequestHeader(name, header[name]);
			}
		}
	}
	
	function onStateChange(obj, state){
		if(obj.readyState == 4){
        	if ((obj.status >= 200 && obj.status < 300) || obj.status == 304) {
				state.onFinish();
				var responseData;
	            if (obj.responseText && state.dataType === "json") {
	                try {
	                    responseData = eval("(" + obj.responseText + ")");
	                }
	                catch (e) {
	                    state.onDataError(e, obj.responseText);
	                }
	                state.onDataSuccess(responseData);
	            }
	            else if (obj.responseText && state.dataType === "string") {
	                state.onDataSuccess(obj.responseText);
	            }
			}
			else if(obj.status !== 0){
				state.onError();
			}
		}
	}
	
	
	function Xhr(param){
		this.state = {};
		squl.lang.extend(defaultParam ,this.state);
		squl.lang.extend(param ,this.state);
		//this.init();
		if(!this.state.delay){
			this.send();
		}
	}
	
	Xhr.prototype = {
		init: function(){
			var obj = this.obj = createXHR();
			var state = this.state;
			obj.onreadystatechange = function(){
				onStateChange(obj, state);
			};
		},
		
		send : function(){
			this.init();
			var obj = this.obj,
				state = this.state,
				urlSend = state.url,
				error;
//			if(obj.readyState !== 0){
//				obj.abort();
//			}
			try {
				if (state.type === "get") {
					var isNoParam = (urlSend.indexOf("?") < 0);
					if(!state.cache || state.data !== ""){
						urlSend = isNoParam ? urlSend + "?" : urlSend;
					}
					if(state.data !== ""){
						urlSend = isNoParam ? urlSend + state.data : urlSend + "&" + state.data;
					}
					if(!state.cache){
						urlSend = state.data !== "" || !isNoParam ? urlSend + "&iid=" + Math.random() : urlSend + "iid=" + Math.random();
					}
					obj.open(state.type, urlSend, true);
					setHeader(obj, state);
					obj.send(null);
				}
				else if (state.type === "post") {
					obj.open(state.type, urlSend, true);
					setHeader(obj, state);
					obj.send(state.data);
				}
			}
			catch (e){
				state.onSendError();
				state.onError();
				error = true;
			}
			
			if(!error){
				clearTimeout(state.timer);
				state.timer = setTimeout(function(){
					 if (obj.readyState !== 4) {
					 	obj.abort();
						state.onTimeout();
						state.onError();
					 }
				}, state.timeout);

			}

		},
		
		abort : function(){
			var obj = this.obj;
			if(obj && obj.readyState !== 0){
				obj.abort();
				clearTimeout(this.state.timer);
			}
		},
		
		setParam : function(param){
			squl.lang.extend(param ,this.state);
		},
		
		getParam : function(name){
			return this.state[name];
		},
		
		get : function(name){
			return this.obj[name];
		}
	}
	//导出
	var Ajax = Xhr;
//----------AjaxObj----------->>

	tcExec.ajax = function(param){
		return new Ajax(param);
	}

	
	
	
	
	
    squl.add("exec", tcExec);
    

});


//<<----------------动画核心-----------------
	
	squl.use("exec", function(squl){
		var PI = Math.PI;
	
		var ani = function(param, query){
			var thisObj = squl.lang.pro(_ani);
			var defaultParam = {
				elem : 	query != null ? this.dom(query) : this,
				from : {},
				to : {},
				onRun : function(){}, //开始的回调
				onFinish : function(){},//正常结束的回调
				onPause : function(){},//暂停的回调
				onStop : function(){},//手动停止的回调
				onBreak : function(){},//被打断的回调
				time : 1000,
				interval : 10,
				acc : "o",
				retrace : 0,
				speedUp : true,
				delay : false
			};
			squl.lang.extend(param, defaultParam, true);
			squl.lang.extend(defaultParam, thisObj);
			thisObj.init();
			return thisObj;
		}
		
		
		var _ani = {
			_checkNeedComp : function(param){
				var found = false;
				for(name in param){
					if(param.hasOwnProperty(name) && "time interval acc retrace speedUp".indexOf(name) !== -1){
						found = true;
					}
				}
				return found;
			},
			
			
			/**
			 * 初始化
			 */
			init : function(param, forceDelay){
				var needComp = true;
				
				if(param){
					needComp = this._checkNeedComp(param);
					squl.lang.extend(param, this);
					
					//if(param.to || param.from){
						//强制重新计算变换数组
					//	this._pushToAct(true);
					//}
				}
				
				//变换数组
				this.aniArray = [];
				this._pushToAct();
				
				
				//当前的步进
				this.now = 0;
				if(needComp){
					//步进数
					this.step = 0;
					//运动路径系数
					this.path = [];
					this.compPath();
				}
				
				if(!this.delay && !forceDelay){
					this.run();
				}
			},
			
			/**
			 * 计算运动路径
			 */
			compPath : function(){
				//向上舍入
				var step = this.step = Math.ceil(this.time/this.interval),
					path = this.path;
				//初始路径为开始处
				path[0] = 0;
				switch(this.acc){
					case 1:
						//匀加速
						for(var n=1; n<=step; n++){
							path[n] = Math.pow(n/step, 2);
						}
						break;
					case 2:
						//匀加加速
						for(var n=1; n<=step; n++){
							path[n] = Math.pow(n/step, 3);
						}
						break;
					case "o":
						//圆周
						if(this.retrace === 0){
							var nPI = PI/2;
							for(var n=1; n<=step; n++){
								path[n] = Math.sin(n/step*nPI);
							}
						}
						else{
							var swing = (1+retrace)*PI/4,
								swingSin = Math.sin(PI/4*swing);
							for(var n=1; n<=step; n++){
								path[n] = Math.sin(n/step*swing)/swingSin;
							}
						}
						break;
					default :
						//匀速
						for(var n=1; n<=step; n++){
							path[n] = n/step;
						}
						break;
				}
				//如果是减速，反转这个数组
				if(!this.speedUp){
					path.reverse();
					for(var n=0; n<path.length; n++){
						path[n] = 1 - path[n];
					}
				}
			},
			
			/**
			 * 把运动对象保存起来
			 * @param {Bullean} forceComp 是否强制计算
			 */
			_pushToAct : function(forceComp){
				var fromObj = this.from, fromValue, tempFrom = {},
					toObj = this.to, toValue;
				for(name in toObj){
					//支持from为空的情况，这时，from参数为当前值
					if(toObj.hasOwnProperty(name)){
						if(!fromObj.hasOwnProperty(name) || forceComp){
							//TODO:支持多节点的动画,每个elem的属性会保存在节点数组里面
							tempFrom[name] = this.elem.realStyle(name);
						}
						else{
							tempFrom[name] = fromObj[name];
						}
						//保存变化值
						if(typeof toObj[name] === "number"){
							fromValue = formatStyle(tempFrom[name]);
							toValue = toObj[name];	
						}
						else{
							fromValue = formatStyle(tempFrom[name]);
							toValue = formatStyle(toObj[name]);
						}
                        this.aniArray.push({
                            type: name,
                            from: fromValue,
                            delta: toValue - fromValue,
							unit : getUnit(toObj[name])
                        });
					}
				}
			},
			
			/**
			 * 设置ani对象的属性
			 * @param {Object} param
			 */
			set : function(param){
				this.init(param, true);
			},
			

			/**
			 * 执行运动，可以设置运动对象的属性
			 * @param {Object} param
			 */
			run : function(param){
				if(param){
					this.set(param);
				}
				this._aniCore();
				//暂停的回调
				this.onRun();
			},
			
			stop : function(){
				if(this.timer){
					clearInterval(this.timer);
					this.timer = null;
					//中断的回调
					this.onStop();
				}
				this.now = 0;
			},
			
			pause : function(){
				if(this.timer){
					clearInterval(this.timer);
					this.timer = null;
					//暂停的回调
					this.onPause();
				}
			},
			
			goon : function(){
				if(!this.timer){
					_aniCoreTime();
					this.timer = setInterval(_aniCoreTime, this.interval);
				}
			},
			
			_aniCore : function(){
				this.stop();
				this._aniCoreTime();
				var that = this;
				this.timer = setInterval(function(){
					that._aniCoreTime.call(that);
				}, this.interval);
			},
			
			_aniCoreTime : function(){
				var ani, nowValue, styleValue;
				for(var n=0; n<this.aniArray.length; n++){
					ani = this.aniArray[n];
					
					if(ani.from[0]){
						//如果ani.from是数组的话，执行单独的动画
						for(var i=0;i<ani.from.length;i++){
							nowValue = ani.delta*this.path[this.now] + ani.from[i];
							//有代码冗余
							if(ani.unit === null){
								//属性为纯数字
								styleValue = nowValue;
							}
							else if(ani.unit.index){
								//属性中的数字值不在index = 0 的时候，比如ie的 filter : alpha(opacity=0)
								styleValue = ani.unit.array[0] + nowValue + ani.unit.array[1];
							}
							else{
								//属性中的数字值在index = 0 的时候，left : 15px;
								styleValue = nowValue + ani.unit.str;
							}
							
							if(this.elem[i]){
								this.elem[i].style[ani.type] = styleValue;
							}
						}
					}
					else{
						nowValue = ani.delta*this.path[this.now] + ani.from;
						
						if(ani.unit === null){
							//属性为纯数字
							styleValue = nowValue;
						}
						else if(ani.unit.index){
							//属性中的数字值不在index = 0 的时候，比如ie的 filter : alpha(opacity=0)
							styleValue = ani.unit.array[0] + nowValue + ani.unit.array[1];
						}
						else{
							//属性中的数字值在index = 0 的时候，left : 15px;
							styleValue = nowValue + ani.unit.str;
						}
						for(var i=0;i<this.elem.length;i++){
							this.elem[i].style[ani.type] = styleValue;
						}
					}
				}
				this.now++;
				
				if(this.now > this.step){
					clearInterval(this.timer);
					this.timer = null;
					//中断的回调
					this.onFinish();
				}
			}
		}
		
		
		
		function formatStyle(array){
			if(array.push){
				for(var n=0; n<array.length; n++){
					array[n] = parseFloat(array[n].replace(/[^0123456789.-]+/g, ""), 10);
				}
			}
			else if(typeof array === "string"){
				array = parseFloat(array.replace(/[^0123456789.-]+/g, ""), 10);
			}
			return array;
		}
		
		/**
		 * 获取style值的单位
		 * @param {Object} value
		 */
		function getUnit(value){
			if(typeof value === "string"){
				var format = value.replace(/(\d|\.|\-)+/, "xxx");
				return {
					str : value.replace(/(\d|\.|\-)+/, ""),
					index : format.indexOf("xxx"),
					array : format.split("xxx")
				};
			}
			else{
				return null;
			}
		}
		
		squl.add({ani:ani});
	});
	//----------------动画核心----------------->>

	//---------------realStyle------------->>
	squl.use("exec",function(squl){
		function getRealStyle(el,styleProp)
		{
			if (el.currentStyle)
				var y = el.currentStyle[styleProp];
			else if (window.getComputedStyle)
				var y = document.defaultView.getComputedStyle(el,null).getPropertyValue(styleProp);
			return y;
		}
		
		function realWidth(domQuery){
			var execElems = domQuery != undefined ? this.dom(domQuery) : this,
				length = execElems.length, elem;
			var returnValue = [];
            for (var n = 0; n < length; n++) {
				elem = execElems[n];
				//为none时获取不到宽度
                if (getRealStyle(elem, "display") !== "none") {
                    returnValue.push(elem.offsetWidth || elem.clientWidth);
                }
                else {
                    elem.style.display = "block";
                    returnValue.push(elem.offsetWidth || elem.clientWidth);
                    elem.style.display = "none";
                }
            }
			return returnValue;
		}
		
		function realHeight(domQuery){
			var execElems = domQuery != undefined ? this.dom(domQuery) : this,
				length = execElems.length, elem;
			var returnValue = [];
            for (var n = 0; n < length; n++) {
				elem = execElems[n];
				//为none时获取不到宽度
                if (getRealStyle(elem, "display") !== "none") {
                    returnValue.push(elem.offsetHeight || elem.clientHeight);
                }
                else {
                    elem.style.display = "block";
                    returnValue.push(elem.offsetHeight || elem.clientHeight);
                    elem.style.display = "none";
                }
            }
			return returnValue;
		}

		function realStyle(type, domQuery){
			var execElems = domQuery != undefined ? this.dom(domQuery) : this,
				length = execElems.length, elem;
			var returnValue = [];
			if(type === "display"){
	            for (var n = 0; n < length; n++) {
					elem = execElems[n];
	                returnValue.push(getRealStyle(elem, type));
	            }
			}
			else{
	            for (var n = 0; n < length; n++) {
					elem = execElems[n];
					//为none时获取不到宽度
	                if (getRealStyle(elem, "display") !== "none") {
	                    returnValue.push(getRealStyle(elem, type));
	                }
	                else {
	                    elem.style.display = "block";
	                    returnValue.push(getRealStyle(elem, type));
	                    elem.style.display = "none";
	                }
	            }
			}
			
			return returnValue;
		}

		
        squl.add({
            realStyle: realStyle,
			realWidth: realWidth,
			realHeight: realHeight
        });
	});

//----------------------------tc_exec_0.1--------------------------------//

//<<------------------Cookie-------------------------
squl.use("comp", function(squl){
	
	//from jQuery
	var cook = function(name, value, options) {
	if (typeof value != 'undefined' && (typeof value == 'string' || typeof value == 'number')) { // name and value given, set cookie
	        options = options || {};
	        if (value === null) {
	            value = '';
	            options.expires = -1;
	        }
	        var expires = '';
	        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
	            var date;
	            if (typeof options.expires == 'number') {
	                date = new Date();
	                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
	            } else {
	                date = options.expires;
	            }
	            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
	        }
	        var path = options.path ? '; path=' + (options.path) : '';
	        var domain = options.domain ? '; domain=' + (options.domain) : '';
	        var secure = options.secure ? '; secure' : '';
	        document.cookie = [name, '=', options.encode ? encodeURIComponent(value) : value, expires, path, domain, secure].join('');
	    } else { // only name given, get cookie
	        var cookieValue = null;
	        if (document.cookie && document.cookie != '') {
	            var cookies = document.cookie.split(';');
	            for (var i = 0; i < cookies.length; i++) {
	                var cookie = trim(cookies[i]);
	                // Does this cookie string begin with the name we want?
	                if (cookie.substring(0, name.length + 1) == (name + '=')) {
						
						if(value){
							try{
			                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
							}
							catch(e){
								cookieValue = "";
							}
						}
						else{
			                cookieValue = cookie.substring(name.length + 1);
						}
						
						
	                    break;
	                }
	            }
	        }
	        return cookieValue;
	    }
	};
	
	var main = {
		
		/**
		 * 
		 * @param {String} name 键名
		 * @param {String} value 键值
		 * @param {Number} expires 过期天数
		 * @param {Object} option  参数包含： 	.path 路径
		 * 										.domain 域
												.secure 保护
												.encode 是否进行编码
		 */
	    set : function (name, value, expires, option) {
			var dParam = {
				encode:true,
				expires:expires
			}
			squl.lang.extend(option, dParam);
	        cook(name, value, dParam);
	    },
	    get: function (name, decode) {
			var decodeIt = decode == undefined ? true : false;
	        return cook(name, decodeIt);
	    },
	    del: function (name) {
	        var d = new Date();
	        d.setTime(d.getTime() - 3600 * 1000 * 24);
	        this.set(name, "", d);
	    },
	    getChild: function (key, childKey, decode) {
			var decodeIt = decode == undefined ? true : false;
			
	        var child = this.get(key, false);
			if(child){
		        var childs = child.split('&');  
		        var val = "",
					tirmChild;  
		        for (var i = 0; i < childs.length; i++) {
					tirmChild = trim(childs[i]);
					if(tirmChild.substring(0, childKey.length + 1) == (childKey + '=')){
						if(decodeIt){
							try{
								val = decodeURIComponent(tirmChild.substr(childKey.length + 1));
							}
							catch(e){
								val = "";
							}
						}
						else{
							val = tirmChild.substr(childKey.length + 1);
						}
						break;
					}
		        }
		        return val;
			}
	    }
	}


	 //去除左空白字符
	var rwhite = /\s/,
		rClass = /[\n\t]/g,
		rSpaces = /\s+/,
		trimLeft = /^\s+/,
		trimRight = /\s+$/;

	// 测试能不能索引到\s
	// (IE会索引失败)
	if ( !rwhite.test( "\xA0" ) ) {
		trimLeft = /^[\s\xA0]+/;
		trimRight = /[\s\xA0]+$/;
	}

	// 如果有的话就使用原生的 String.trim 
	var trim = String.prototype.trim ?
		function( text ) {
			return text == null ? "" : String.prototype.trim.call( text );
		} :
		// 没有的话用我们自己的
		function( text ) {
			return text == null ?
				"" :
				text.toString().replace( trimLeft, "" ).replace( trimRight, "" );
		};


	
	
	
	squl.add({cookie:main});
	
});

//------------------Cookie------------------------->>



//<<-----------------clear，清除squl对象-----------------------
squl.use("exec", function(squl){
	squl.add({clear:function(){
		for(var n=0; n < this.length; n++){
			this[n] = null;
		}
		this.length = 0;
	}});
});
//-------------------clear----------------------->>



/**
 * @author kares
 * @version 2011-04-01 登录模块，用于CN，今天是愚人节
 */

//<<-----------------登录模块----------------------
(function(window){
    /**
     *  MD5 (Message-Digest Algorithm)
     *  http://www.webtoolkit.info/
     */
    var MD5 = function(string){
        function RotateLeft(lValue, iShiftBits){
            return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
        }
        
        function AddUnsigned(lX, lY){
            var lX4, lY4, lX8, lY8, lResult;
            lX8 = (lX & 0x80000000);
            lY8 = (lY & 0x80000000);
            lX4 = (lX & 0x40000000);
            lY4 = (lY & 0x40000000);
            lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
            if (lX4 & lY4) {
                return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
            }
            if (lX4 | lY4) {
                if (lResult & 0x40000000) {
                    return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                }
                else {
                    return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
                }
            }
            else {
                return (lResult ^ lX8 ^ lY8);
            }
        }
        
        function F(x, y, z){
            return (x & y) | ((~ x) & z);
        }
        function G(x, y, z){
            return (x & z) | (y & (~ z));
        }
        function H(x, y, z){
            return (x ^ y ^ z);
        }
        function I(x, y, z){
            return (y ^ (x | (~ z)));
        }
        
        function FF(a, b, c, d, x, s, ac){
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };
        
        function GG(a, b, c, d, x, s, ac){
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };
        
        function HH(a, b, c, d, x, s, ac){
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };
        
        function II(a, b, c, d, x, s, ac){
            a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
            return AddUnsigned(RotateLeft(a, s), b);
        };
        function ConvertToWordArray(string){
            var lWordCount;
            var lMessageLength = string.length;
            var lNumberOfWords_temp1 = lMessageLength + 8;
            var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
            var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
            var lWordArray = Array(lNumberOfWords - 1);
            var lBytePosition = 0;
            var lByteCount = 0;
            while (lByteCount < lMessageLength) {
                lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                lBytePosition = (lByteCount % 4) * 8;
                lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
                lByteCount++;
            }
            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
            lBytePosition = (lByteCount % 4) * 8;
            lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
            lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
            lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
            return lWordArray;
        };
        
        function WordToHex(lValue){
            var WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
            for (lCount = 0; lCount <= 3; lCount++) {
                lByte = (lValue >>> (lCount * 8)) & 255;
                WordToHexValue_temp = "0" + lByte.toString(16);
                WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
            }
            return WordToHexValue;
        };
        
        function Utf8Encode(string){
            string = string.replace(/\r\n/g, "\n");
            var utftext = "";
            
            for (var n = 0; n < string.length; n++) {
            
                var c = string.charCodeAt(n);
                
                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if ((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                
            }
            
            return utftext;
        };
        
        var x = Array();
        var k, AA, BB, CC, DD, a, b, c, d;
        var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
        var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
        var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
        var S41 = 6, S42 = 10, S43 = 15, S44 = 21;
        
        string = Utf8Encode(string);
        
        x = ConvertToWordArray(string);
        
        a = 0x67452301;
        b = 0xEFCDAB89;
        c = 0x98BADCFE;
        d = 0x10325476;
        
        for (k = 0; k < x.length; k += 16) {
            AA = a;
            BB = b;
            CC = c;
            DD = d;
            a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
            d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
            c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
            b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
            a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
            d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
            c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
            b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
            a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
            d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
            c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
            b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
            a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
            d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
            c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
            b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
            a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
            d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
            c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
            b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
            a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
            d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
            c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
            b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
            a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
            d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
            c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
            b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
            a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
            d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
            c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
            b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
            a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
            d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
            c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
            b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
            a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
            d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
            c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
            b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
            a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
            d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
            c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
            b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
            a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
            d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
            c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
            b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
            a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
            d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
            c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
            b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
            a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
            d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
            c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
            b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
            a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
            d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
            c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
            b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
            a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
            d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
            c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
            b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
            a = AddUnsigned(a, AA);
            b = AddUnsigned(b, BB);
            c = AddUnsigned(c, CC);
            d = AddUnsigned(d, DD);
        }
        
        var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);
        
        return temp.toLowerCase();
    }

	window.MD5 = MD5;

//<<--------AjaxObj-------------
	/**
	 * 创建XHR对象
	 */
    function createXHR(){
        if (typeof XMLHttpRequest != "undefined") {
            return new XMLHttpRequest();
        }
        else if (typeof ActiveXObject != "undefined") {
            if (typeof arguments.callee.activeXString != "string") {
                var versions = ["MSXML2.XMLHttp.6.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp"];
                for (var i = 0, len = versions.length; i < len; i++) {
                    try {
                        var xhr = new ActiveXObject(versions[i]);
                        arguments.callee.activeXString = versions[i];
                        return xhr;
                    } 
                    catch (ex) {}
                }
            }
            return new ActiveXObject(arguments.callee.activeXString);
        }
        else {}
    }	
	var defaultParam = {
		delay:false,
		timeout : 8000,
		type:"get",
		url:"",
		data:"",
		dataType:"json",
		cache:false,
		onTimeout : function(){},
		onFinish : function(){},
		onError : function(){},
		onSendError : function(){},
		onDataSuccess : function(){},
		onDataError : function(){},
		requestHeader : {
			"Content-Type":"application/x-www-form-urlencoded"
		}
	}
	
	function setHeader(obj, state){
		var header = state.requestHeader;
		for(name in header){
			if(header.hasOwnProperty(name)){
				obj.setRequestHeader(name, header[name]);
			}
		}
	}
	
	function onStateChange(obj, state){
		if(obj.readyState == 4){
        	if ((obj.status >= 200 && obj.status < 300) || obj.status == 304) {
				state.onFinish();
				var responseData;
	            if (obj.responseText && state.dataType === "json") {
	                try {
	                    responseData = eval("(" + obj.responseText + ")");
	                }
	                catch (e) {
	                    state.onDataError(e, obj.responseText);
	                }
	                state.onDataSuccess(responseData);
	            }
	            else if (obj.responseText && state.dataType === "string") {
	                state.onDataSuccess(obj.responseText);
	            }
			}
			else if(obj.status !== 0){
				state.onError();
			}
		}
	}
	
	
	function Xhr(param){
		this.state = {};
		extend(defaultParam ,this.state);
		extend(param ,this.state);
		//this.init();
		if(!this.state.delay){
			this.send();
		}
	}
	
	Xhr.prototype = {
		init: function(){
			var obj = this.obj = createXHR();
			var state = this.state;
			obj.onreadystatechange = function(){
				onStateChange(obj, state);
			};
		},
		
		send : function(){
			this.init();
			var obj = this.obj,
				state = this.state,
				urlSend = state.url,
				error;
//			if(obj.readyState !== 0){
//				obj.abort();
//			}
			try {
				if (state.type === "get") {
					var isNoParam = (urlSend.indexOf("?") < 0);
					if(!state.cache || state.data !== ""){
						urlSend = isNoParam ? urlSend + "?" : urlSend;
					}
					if(state.data !== ""){
						urlSend = isNoParam ? urlSend + state.data : urlSend + "&" + state.data;
					}
					if(!state.cache){
						urlSend = state.data !== "" || !isNoParam ? urlSend + "&iid=" + Math.random() : urlSend + "iid=" + Math.random();
					}
					obj.open(state.type, urlSend, true);
					setHeader(obj, state);
					obj.send(null);
				}
				else if (state.type === "post") {
					obj.open(state.type, urlSend, true);
					setHeader(obj, state);
					obj.send(state.data);
				}
			}
			catch (e){
				state.onSendError();
				state.onError();
				error = true;
			}
			
			if(!error){
				clearTimeout(state.timer);
				state.timer = setTimeout(function(){
					 if (obj.readyState !== 4) {
					 	obj.abort();
						state.onTimeout();
						state.onError();
					 }
				}, state.timeout);

			}

		},
		
		abort : function(){
			var obj = this.obj;
			if(obj && obj.readyState !== 0){
				obj.abort();
				clearTimeout(this.state.timer);
			}
		},
		
		setParam : function(param){
			extend(param ,this.state);
		},
		
		getParam : function(name){
			return this.state[name];
		},
		
		get : function(name){
			return this.obj[name];
		}
	}
	//导出
	var Ajax = Xhr;
	window.Ajax = Ajax;
//----------AjaxObj----------->>
	
	//一些用于query的正则
	var rwhite = /\s/,
		rClass = /[\n\t]/g,
		rSpaces = /\s+/,
		//去除左空白字符
		trimLeft = /^\s+/,
		trimRight = /\s+$/;

	// 测试能不能索引到\s
	// (IE会索引失败)
	if ( !rwhite.test( "\xA0" ) ) {
		trimLeft = /^[\s\xA0]+/;
		trimRight = /[\s\xA0]+$/;
	}

	// 如果有的话就使用原生的 String.trim 
	var trim = String.prototype.trim ?
		function( text ) {
			return text == null ? "" : String.prototype.trim.call( text );
		} :
		// 没有的话用我们自己的
		function( text ) {
			return text == null ?
				"" :
				text.toString().replace( trimLeft, "" ).replace( trimRight, "" );
		};

	


    /** 扩展对象，仅适用在单层的扩展中
     * @param {Object} merge 来源对象
     * @param {Object} tar 扩展的目标对象
     * @param {Boolean} safe 是否进行安全的扩展，只扩展目标对象中已有的属性
     */
    var extend = function(merge, tar, safe){
		var already;
		if(!safe){
			already = function(){return true;};
		}
		else{
			already = function(obj, proper){
				return obj.hasOwnProperty(proper);
			}
		}
        if (merge != null && tar != null) {
            var src, copy, name;
            for (name in merge) {
                if (merge.hasOwnProperty(name)) {
                    copy = merge[name];
                    if (tar === copy) {
                        continue;
                    };
					//只覆盖已定义的属性？
                    if (copy !== undefined && already(tar, name)) {
                        tar[name] = copy;
                    };
                };//if-END
            };//for-END
            return tar;
        }//if-END
    };

	/**
	 * 格式化表单
	 * @param {DOM} form 要格式化的表单元素
	 */
    function serialize(form){
        var parts = new Array();
        var field = null;
        for (var i = 0, len = form.elements.length; i < len; i++) {
            field = form.elements[i];
			if(field.name === ""){
				continue;
			}
            switch (field.type) {
				//case "select-one":
                case "select-multiple":
                    for (var j = 0, optLen = field.options.length; j < optLen; j++) {
                        var option = field.options[j];
                        if (option.selected) {
                            var optValue = "";
                            if (option.hasAttribute) {
                                optValue = (option.hasAttribute("value") ? option.value : option.text);
                            }
                            else {
                                optValue = (option.attributes["value"].specified ? option.value : option.text);
                            }
                            parts.push(encodeURIComponent(field.name) + "=" +
                            encodeURIComponent(optValue));
                        }
                    }
                    break;
                //case undefined:
                //case "file":
                //case "submit":
                //case "reset": 
                //case "radio":
				case "password": //密码密文MD5一次
					parts.push(encodeURIComponent(field.name) + "=" +
                    encodeURIComponent(MD5(field.value)));
					break;
                case "button": 
                    break;
                case "checkbox":
                    if (!field.checked) {
                        break;
                    }
                /* 默认格式 */
                default:
                    parts.push(encodeURIComponent(field.name) + "=" +
                    encodeURIComponent(field.value));
            }
        }
        return parts.join("&");
    }
	

		
		var strHost;
		if (window.location.host.indexOf("192.168.") != -1) {
			strHost = window.location.hostname;
		}
		else if (window.location.host.indexOf("172.16.") != -1) {
			strHost = window.location.hostname;
		}
		else if(window.location.host.indexOf("17u.cn") != -1){
			strHost = ".17u.cn";
		}
		else{
			strHost = "";
		}


	function setCookie(name,value,days) {
		if (days) {
			expires = days;
		}
		else var expires = "";
		squl.cookie.set(name, value, expires, {domain:strHost, encode:false, path:"/"});
		//document.cookie = name+"="+ encodeURI(value) + expires +"; path=/"+"; domain=" + strHost;
	}
	
	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return decodeURI(c.substring(nameEQ.length,c.length));
		}
		return null;
	}
	
	function delCookie(name) {
		setCookie(name,"",-1);
	}


//<<-------------登录对象--------------






	function get(id){
		return document.getElementById(id);
	}
	//闭包捕获
	function closer(that, fn){
		return function(){
			fn.apply(that, arguments);
		}
	}
	
	function preventDefault(event){
		var e = event ? event : window.event;
       	if (e.preventDefault) {
            e.preventDefault();
        }
        else {
            e.returnValue = false;
        }
	}
	
	function setTips(obj, text){
		if(obj){
			obj.style.visibility = "visible";
			obj.innerHTML = text;
		}
	}

	//<<--------行为对象-----
	function logObj(param){
		var defaultParam = {
			url : "", //请求地址
			timeout : 8000, //超时时间 
			inFormId : "", //登录表单的id
			outFormId : "#", //退出表单的id(可选)
			nameInputId : "", //用户名input
			passWordInputId : "", //密码input
			remebInputId : "", //记住密码input
			loginBtnId : "", //登录按钮
			waitLoginId : "", //登录中的等待
			logoutBtnId : "#", //退出按钮(可选)
			waitLogoutId : "#", //退出中的等待(可选)
			tipsId : "#", //提示信息(可选)
			userNameId : "#",//用户名id(可选)
			onSuccessLogin : function(){}, //成功登录的回调(可选)
			onSuccessExit : function(){}, //成功退出的回调(可选)
			onPassFailed : function(){}, //密码失败的回调(可选)
			onNameFailed : function(){}, //用户名错的回调(可选)
			onLoginFailed : function(){}, //退出失败的回调(可选)
			onExitFailed : function(){}, //退出失败的回调(可选)
			onLogining : function(){}, //登录开始的回调(可选)
			onExiting : function(){}, //退出开始的回调(可选),
			onAbort : function(){}, //手动中断时的回调
			onTimeout : function(){} //超时回调(可选)
		}
		extend(param, defaultParam, true);
		extend(defaultParam, this);
		this.init();
	}
	
	logObj.prototype = {
		set : function(param){
			extend(param, this);
		},
		
		init : function(){
			this.inForm = get(this.inFormId);
			this.outForm = get(this.outFormId);
			this.nameInput = get(this.nameInputId);
			this.passWordInput = get(this.passWordInputId);
			this.remebInput = get(this.remebInputId);
			this.loginBtn = get(this.loginBtnId);
			this.logoutBtn = get(this.logoutBtnId);
			this.waitLogin = get(this.waitLoginId);
			this.waitLogout = get(this.waitLogoutId);
			this.tips = get(this.tipsId);
			this.userName = get(this.userNameId);
			
			
			var that = this;
			
			//登录
			this.inForm.onsubmit = function(event){
				preventDefault(event);
			};
			this.loginBtn.onclick = function(){
				that.login.call(that);
			};
			//退出
			if(this.outForm){
				this.outForm.onsubmit = function(event){
					preventDefault(event);
				};
			}
			if(this.logoutBtn){
				this.logoutBtn.onclick = function(){
					that.exit.call(that);
				};	
			}

			this.nameInput.setAttribute("placeholder", "手机/邮箱");
			
			this.nameInput.onfocus = 
			this.passWordInput.onfocus = function(event){
                this.value = trim(this.value);
                if(!('placeholder' in document.createElement('input')) && this.getAttribute("placeholder")){
                    if(this.value === this.getAttribute("placeholder")){
                        this.value = "";
                    }
                    this.style.color = "#000000";
                }
				this.select();
			}
            var fnName;
            this.nameInput.onblur = fnName = function(){
                if(!('placeholder' in document.createElement('input')) && this.getAttribute("placeholder")){
                    this.value = trim(this.value);
                    if(this.value === ""){
                        this.style.color = "#666666";
                        this.value = this.getAttribute("placeholder");
                    }
                    else if(this.value === this.getAttribute("placeholder")){
                        this.style.color = "#666666";
                    }
                    else{
                        this.style.color = "#000000";
                    }
                }
            };
            fnName.apply(this.nameInput);
			
			this.nameInput.onkeydown = 
			this.passWordInput.onkeydown = 
			this.remebInput.onkeydown = function(e){
				//preventDefault(e);
				var keycode = window.ActiveXObject ? event.keyCode : e.which;
	　　         if(keycode == 13){
					preventDefault(e);
					that.login();
	　　         }
			}
			
			//创建逻辑核心
			this.lCore = new LCore({
				url : this.url,
				timeout : this.timeout,
				//都和超时出错的提示一样
				onTimeout : closer(that, that.onTimeout),
				onError: closer(that, that.onFailed),
				onDataError: closer(that, that.onFailed),
				onSendError: closer(that, that.onFailed),
				onLoginCallBack: closer(that, that.onLoginCallBack),
				onExitCallBack: closer(that, that.onExitCallBack),
				onStartLogin: closer(that, that.onStartLogin),
				onStartExit : closer(that, that.onStartExit),
				onAbort : closer(that, that.onAbort)
			});
			
			
		},
		
		showLogining : function(){
			this.loginBtn.style.display = "none";
			this.waitLogin.style.display = "block";
			if(this.tips){
				this.tips.style.visibility = "hidden";
			}
			
			this.nameInput.disabled = 
			this.passWordInput.disabled = 
			this.remebInput.disabled = "disabled";
		},
		showLoginNormal : function(){
			this.loginBtn.style.display = "inline";
			this.waitLogin.style.display = "none";
			
			this.nameInput.disabled = 
			this.passWordInput.disabled = 
			this.remebInput.disabled = null;
		},
		
		showExiting : function(){
			if(this.logoutBtn){
				this.logoutBtn.style.display = "none";
			}
			if(this.waitLogout){
				this.waitLogout.style.display = "block";
			}
			
		},
		showExitNormal : function(){
			if(this.logoutBtn){
				this.logoutBtn.style.display = "inline";
			}
			if(this.waitLogout){
				this.waitLogout.style.display = "none";
			}
		},
		
		logState: function(){
			return this.lCore.logState();
		},
		
		login : function(){
			if(trim(this.nameInput.value).length === 0){
				setTips(this.tips, "请输入您的用户名");
				this.onLoginFailed();
			}
			else if(trim(this.passWordInput.value).length === 0){
				setTips(this.tips, "请输入您的密码");
				this.onLoginFailed();
			}
			else{
				this.lCore.login(serialize(this.inForm), this.timeout);
				//清空密码
				this.passWordInput.value = "";
			}
		},
		
		exit : function(){
			var data = "";
			if(this.outForm){
				data = serialize(this.outForm);
			}
			this.lCore.exit(data, this.timeout);
		},
		
		abort : function(){
			if(this.logState() === "login" ){
				this.showLoginNormal();
			}
			else if(this.logState() === "exit" ){
				this.showExitNormal();
			}
			if(this.tips){
				this.tips.style.visibility = "hidden";
			}
			this.lCore.abort();
		},
		
		onFailed : function(data){
			if(this.logState() === "login" ){
				this.showLoginNormal();
				this.onLoginFailed(data);
				setTips(this.tips, "抱歉，登录失败，请重试。");
			}
			else if(this.logState() === "exit" ){
				this.showExitNormal();
				this.onExitFailed(data);
			}
		},
		//一系列事件
		onLoginCallBack : function(data){
			this.showLoginNormal();
			switch (data.state){
				case 100 :
					this.onSuccessLogin(data);
					if(this.userName){
						this.userName.innerHTML = data.name;
					}
					break;
				case 200 :
					this.onPassFailed(data);
					this.onLoginFailed(data);
					this.passWordInput.select();
					setTips(this.tips, "用户名或密码错误，请重新输入。");
					break;
				case 300 :
					this.onNameFailed(data);
					this.onLoginFailed(data);
					this.nameInput.select();
					setTips(this.tips, "用户名或密码错误，请重新输入。");
					break;
				default : 
					this.onFailed(data);
			}
		},
		onExitCallBack : function(data){
			this.showExitNormal();
			//隐藏提示信息
			if(this.tips){
				this.tips.style.visibility = "hidden";
			}
			switch (data.state){
				case 100 :
					this.onSuccessExit(data);
					break;
				default : 
					this.onFailed(data);
			}
		},
		
		onStartLogin : function(){
			this.onLogining();
			this.showLogining();
		},
		
		onStartExit : function(){
			this.onExiting();
			this.showExiting();
		}
		
		
		
	}
	//--------行为对象---->>
	
	
	//<<-------逻辑对象----
	
	function LCore(param){
		var defaultParam = {
			url : "",
			timeout : 8000,		
			//连接超时
			onTimeout : function(){},
			//退出时的回调
			onAbort : function(){},
			//连接返回出错的回调
			onError : function(){},
			//数据返回出错的回调
			onDataError : function(){},
			//发送失败的回调
			onSendError : function(){},
			//登录信息返回后的回调
			onLoginCallBack : function(){},
			//注销信息返回后的回调
			onExitCallBack : function(){},		
			//开始登录的回调
			onStartLogin : function(){},			
			//开始注销的回调
			onStartExit : function(){}
		}
		extend(param, defaultParam, true);
		extend(defaultParam, this);
		this.init();
	}
	
	
	LCore.prototype = {
		init : function(){
			var that = this;
			this.ajaxObj = new Ajax({
				url : this.url,
				delay : true,
				timeout : this.timeout,
				onDataSuccess : function(data){
					if(that.logState() === "login" ){
						that.onLoginCallBack(data);
					}
					else if(that.logState() === "exit" ){
						that.onExitCallBack(data);
					}
				},
				onTimeout : that.onTimeout,
				onError : that.onError,
				onDataError : that.onDataError,
				onSendError : that.onSendError
			});
		},
		
		//检查当前状态
		logState : function(){
			if(this.ajaxObj.state.data.indexOf("action=login") >= 0){
				return "login";
			}
			else if(this.ajaxObj.state.data.indexOf("action=exit") >= 0){
				return "exit";
			}
		},
		
		//登录开始
		login : function(data, timeout){
			this.onStartLogin();
			var addParam = data !== "" ? "&" : "";
			this.setCookie(data);
			this.ajaxObj.setParam({data:data+addParam+"action=login", timeout:timeout});
			this.ajaxObj.send();
			//this.logining = true;
		},
		
		
		//注销开始
		exit : function(data, timeout){
			this.onStartExit();
			var addParam = data !== "" ? "&" : "";
			this.ajaxObj.setParam({data:data+addParam+"action=exit", timeout:timeout});
			this.ajaxObj.send();
		},
		
		abort : function(){
			this.ajaxObj.abort();
			this.onAbort();
		},
		
		//使用data数据中的remIt键值来作为保存天数，默认7天
		setCookie : function(data){
			var dataArray = data.split("&"), days = 7, equalIndex, save = true;
			for(var n=dataArray.length-1; n >= 0; n--){
				if(	dataArray[n].indexOf("remIt") >= 0 &&
					dataArray[n].indexOf("remIt") <= (equalIndex = dataArray[n].indexOf("=")) ){
					days = parseInt(dataArray[n].slice(equalIndex+1));
					if(isNaN(days)){
						delCookie("loginRecord");
						return;
					}
					dataArray.splice(n, 1);
					save = false;
					break;
				}
			}
			if(save){
				delCookie("loginRecord");
			}
			else{
				setCookie("loginRecord", dataArray.join("&"), days);
			}
		}
		
		
		
	};
	//------逻辑对象----->>

//---------------登录对象------------>>
	window.trim = trim;
	window.logObj = logObj;
})(window);

//执行book页面头部登录初始化
function initBookLog(tcLog){
					
	//TODO：登录脚本
	document.getElementById("account").disabled = 
	document.getElementById("actpwd").disabled = 
	document.getElementById("rem_it_1w").disabled = null;

	
    function get(id){
        return document.getElementById(id);
    }
	
    get("cancel_img").onclick =
	get("loginText").onclick =
	get("sign_in_cancel").onclick = function(event){
    
        var e = event ? event : window.event;
        if (e.preventDefault) {
            e.preventDefault();
        }
        else {
            e.returnValue = false;
        }
        
        if (get("sign_in_form_backdrop").style.display === "block") {
            loginHide();
			loginHide();
			tcLog.abort();
        }
        else {
            loginShow();
        }
    };
	
    function addEvent(obj, type, fn){
        if (obj) {
            if (obj.addEventListener) {
                obj.addEventListener(type, fn, false);
            }
            else if (obj.attachEvent) {
                var funIe = function(){
                    fn.call(window.event.srcElement);
                };
                obj.attachEvent("on" + type, funIe);
            }
			else{
				obj["on" + type] = fn;
			}
        };
    };
	
	addEvent(document, "click", function(event){
		var e = event ? event : window.event;
		var tar = e.target || e.srcElement;
		var li = get("loginLi");
		while(tar !== document.body && tar !== li && tar !== null){
			tar = tar.parentNode;
		}
		if(tar === document.body){
			if(get("loginBar").style.display === "block"){
				tcLog.abort();
			}
            loginHide();
		}
	});
	

    function loginShow(callback){
			setTimeout(function(){
				//loginShow(true);
				
	    	get("loginText").className = "set_float_on";
			get("loginTextArrow").className = "sign_in_up";
			get("sign_in_form_backdrop").style.display = "block";
	    	get("sign_in_form_box").style.display = "block";
				if(callback){
					callback();
				}
				
			},100);
		
    }
    
    function loginHide(callback, once){
		//loginShow();
    	get("loginText").className = "set_float";
		get("loginTextArrow").className = "sign_in_down";
		if(!once){
			setTimeout(function(){
				loginHide(null, true);
			},1);
		}
		get("sign_in_form_backdrop").style.display = "none";
    	get("sign_in_form_box").style.display = "none";
		if(callback){
			callback();
		}
   }
	
	window.loginHide = loginHide;
	window.loginShow = loginShow;

}


//执行CN_top初始化
function initLog(tcLog){
    function addEvent(obj, type, fn){
        if (obj) {
            if (obj.addEventListener) {
                obj.addEventListener(type, fn, false);
            }
            else if (obj.attachEvent) {
                var funIe = function(){
                    fn.call(window.event.srcElement);
                };
                obj.attachEvent("on" + type, funIe);
            }
			else{
				obj["on" + type] = fn;
			}
        };
    };
	
    function getRelated(event){
		var e = event ? event : window.event;
        if (e.relatedTarget) {
            return e.relatedTarget;
        }
        else if (e.toElement) {
            return e.toElement;
        }
        else if (e.fromElement) {
            return e.fromElement;
        }
        else {
            return null;
        }
    }


    function loginShow(){}
    
    function loginHide(){}
	
    function get(id){
        return document.getElementById(id);
    }

	
	//绑定收藏按钮
	var bookmark;
	if( (bookmark = get("bookmark")) ){
		bookmark.onclick = function(){
			BookmarkApp.addBookmark(bookmark);
		}
	}
	

	
	
	//我的同程
	var ea_mytc_ul_backdrop = get("ea_mytc_ul_backdrop");
	var ea_mytc_ul = get("ea_mytc_ul");
	var myTcArrow = get("myTcArrow");
	var myTcTitle = get("myTcTitle");
	var myTc = get("myTc");
	
	ea_mytc_ul_backdrop.style.width = myTc.clientWidth+"px";
	ea_mytc_ul.style.width = myTc.clientWidth-2+"px";
	
	addEvent(myTcTitle, "mouseover", function(){
		ea_mytc_ul_backdrop.style.display = "block";
		ea_mytc_ul.style.display = "block";
		myTcTitle.className = "set_float_on";
		myTcArrow.className = "mytc_up";
	});
	
	addEvent(myTc, "mouseout", function(event){
		var tar = getRelated(event);
		while(tar !== document.body && tar !== myTc && tar !== null){
			tar = tar.parentNode;
		}
		if(tar === document.body){
			ea_mytc_ul_backdrop.style.display = "none";
			ea_mytc_ul.style.display = "none";
			myTcTitle.className = "set_float";
			myTcArrow.className = "mytc_down";
		}
	});

	//点击其他区域收起气泡
	addEvent(document, "click", function(event){
		var e = event ? event : window.event;
		var tar = e.target || e.srcElement;
		var li = get("myTc");
		while(tar !== document.body && tar !== li && tar !== null){
			tar = tar.parentNode;
		}
		if(tar === document.body){
			ea_mytc_ul_backdrop.style.display = "none";
			ea_mytc_ul.style.display = "none";
			myTcTitle.className = "set_float";
			myTcArrow.className = "mytc_down";
		}
	});

	
	window.loginHide = loginHide;
	window.loginShow = loginShow;

};


//用于弹出层登录
function initBubbleLog(){
	if(squl){
		var dom = '<div style="display:none"><div style="width: 276px; height: 190px; overflow: hidden;"><div id="popup_login_box" class="rules_main"><div class="rules_head"><h3>会员登录</h3><a class="exit_btn" href="#"><img alt="" src="http://img1.40017.cn/cn/comm/images/book/cs_book_common.png"/></a></div><div class="wrap_login_form"><div id="sign_in_form_box_popup"><div class="sign_fm"><form id="login_form_pop" action=""><p id="sign_in_tips_pop" class="sign_tip" style="visibility:hidden;">此账号已注册，请您先登录</p><div class="item"><label for="account_popup">帐号：</label><input id="account_pop" name="name" type="text" maxlength="30" /></div><div class="item"><label for="actpwd_popup">密码：</label><input id="actpwd_pop" name="pass" type="password" maxlength="30" /><a class="sign_lostfound" target="_blank" href="http://www.17u.cn/GetPassword.aspx">忘记密码？</a></div><div class="item"><input type="checkbox" id="rem_it_1w_pop" value="7" name="remIt" /><label class="ri1_label" for="rem_it_1w_pop">记住密码（一周）</label></div><div class="item set_overflow"><button id="sign_in_btn_pop" class="btn_sign_in" title="登录"></button><span id="logining_pop" style="display: none;" class="wait4sign"><img alt="" src="http://img1.40017.cn/cn/new_ui/public/images/wait.gif" />登录中…</span><a id="cancel_pop" class="cancel_sign" href="###">取消</a></div></form></div></div></div></div></div></div>'
		document.write(dom);
		
		//TODO：登录脚本
		document.getElementById("account_pop").disabled = 
		document.getElementById("actpwd_pop").disabled = 
		document.getElementById("rem_it_1w_pop").disabled = null;
		
		//关闭弹出层
		squl.on("click", function(){
			if(jQuery && jQuery.colorbox){
				jQuery.colorbox.close();
			}
		}, "#cancel_pop, #popup_login_box a.exit_btn");
	}
}



//刷新页面
function reFlash(){
    try {
        	if(!document.execCommand('Refresh', false, 0)){
				window.location.reload();
			}
		} 
    catch (BorwerSupportException) {
      	window.location.reload();
    }
}

//加入到收藏夹
BookmarkApp = function () {
    var isIEmac = false; 
    var isMSIE = (navigator.appName == "Microsoft Internet Explorer");
    var cjTitle = document.title;
    var cjHref = location.href;
	
	var isIEs;//遨游ie，360ie
    try {
        window.external.max_invoke("GetHotKey");
        isIEs = true;
    }
    catch (ex) {
        isIEs = false;
    }
	
    var is360 = false;
//    if (navigator.userAgent.toLowerCase().indexOf("360chrome") > -1) {
//        f = true;
//    }
    try {
        if (window.external && window.external.twGetRunPath) {
            var r = external.twGetRunPath();
            if (r && r.toLowerCase().indexOf("360se") > -1) {
                is360 = true;
            }
        }
    } 
    catch (ign) {
        is360 = false;
    }
	
	var isTheWorld = navigator.userAgent.toLowerCase().indexOf("theworld") > -1;
	
 
    function hotKeys() {
        var ua = navigator.userAgent.toLowerCase();
        var str = '';
        var isWebkit = (ua.indexOf('webkit') != - 1);
        var isMac = (ua.indexOf('mac') != - 1);
 
        if (ua.indexOf('konqueror') != - 1) {
            str = 'CTRL + B'; // Konqueror
        }
		else if (window.home || isWebkit || isIEmac || isMac || isIEs) {
            str = (isMac ? 'Command/Cmd' : 'CTRL') + ' + D'; // Netscape, Safari, iCab, IE5/Mac
        }
		else{
			str = 'CTRL + D'; 
		}
        return ((str) ? '抱歉，您的浏览器需要按下 ' + str + ' 来添加书签。' : str);
    }
 
    function isIE8() {
        var rv = -1;
        if (navigator.appName == 'Microsoft Internet Explorer') {
            var ua = navigator.userAgent;
            var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
            if (re.exec(ua) != null) {
                rv = parseFloat(RegExp.$1);
            }
        }
        if (rv > - 1) {
            if (rv >= 8.0) {
                return true;
            }
        }
        return false;
    }
 
    function addBookmark(a) {
        try {
            if (typeof a == "object" && a.tagName.toLowerCase() == "a") {
                a.style.cursor = 'pointer';
                if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function")) {
                    window.sidebar.addPanel(cjTitle, cjHref, ""); // Gecko
                    return false;
                } else if (isMSIE && typeof window.external == "object") {
					//alert(navigator.appVersion);
					
					if(isIEs && !is360 && !isTheWorld){
						alert(hotKeys());
					}
                    else if (isIE8()) {
                        window.external.AddToFavoritesBar(cjHref, cjTitle); // IE 8
                    }
					else {
							window.external.AddFavorite(cjHref, cjTitle); // IE <=7
							//alert(hotKeys());
                    }
                    return false;
                } else if (window.opera) {
                    a.href = cjHref;
                    a.title = cjTitle;
                    a.rel = 'sidebar'; // Opera 7+
                    return true;
                } else {
                    alert(hotKeys());
                }
            } else {
                throw "Error occured.\r\nNote, only A tagname is allowed!";
            }
        } catch (err) {
            //alert(err);
			alert(hotKeys());
        }
 
    }
 
    return {
        addBookmark : addBookmark
    }
	
}();
//-------------------登录模块-------------------->>


//<<-----------refid相关-----------------------
(function(){
	
	function dumpRefid(forceSEKeyWords){
	    var refId = getRefid();
	    var url = "RefId=" + refId, name = 'CNSEInfo', date = new Date();
	    var newArr = AnalyseSearchEngine();
		
		//gb2312的编码必须由服务端转换  JSONP
		if(!forceSEKeyWords && newArr[1] === "gb2312"){
			var sElem = document.createElement("script");
		    sElem.src = "http://www.17u.cn/AjaxHelper/Gb2312ToUtf8.ashx?words="+newArr[2]+"&callback=reDumpRefid";
		    document.getElementsByTagName('head')[0].appendChild(sElem);
			return;
		}
		
		newArr[0] = encodeURIComponent(newArr[0]);
		newArr[2] = forceSEKeyWords ? forceSEKeyWords : newArr[2];
		var referrer = encodeURIComponent( document.referrer );
		
		if(	(referrer.indexOf(".17u.cn") > -1) ||
			(referrer.indexOf("localhost") > -1) ||
			(referrer.indexOf("192.168.") > -1) ||
			(referrer.indexOf("172.16.") > -1) ||
			referrer === ""){
			//如果是站内的跳转或者直接输入，从cookie中获取值
			newArr[0] = encodeURIComponent( squl.cookie.getChild("CNSEInfo", "SEFrom"));
			newArr[2] = encodeURIComponent( squl.cookie.getChild("CNSEInfo", "SEKeyWords"));
			referrer = encodeURIComponent( squl.cookie.getChild("CNSEInfo", "RefUrl") );
		}
		
		//过滤
		referrer = referrer == undefined ? "" : referrer;
		referrer = referrer == "undefined" ? "" : referrer;
		newArr[0] = newArr[0] == undefined ? "" : newArr[0];
		newArr[0] = newArr[0] == "undefined" ? "" : newArr[0];
		newArr[2] = newArr[2] == undefined ? "" : newArr[2];
		newArr[2] = newArr[2] == "undefined" ? "" : newArr[2];
		
		//保存
	    var SEFrom = 'SEFrom=' + newArr[0];
	    var SEKeyWords = 'SEKeyWords=' + newArr[2];
	    var RefUrl = 'RefUrl=' + referrer;
	    var value = url + '&' + SEFrom + '&' + SEKeyWords + '&' + RefUrl;
	    date.setTime(date.getTime() + (12 * 60 * 60 * 1000));
		
		
		var strHost;
		if (window.location.host.indexOf("192.168.") != -1) {
			strHost = window.location.hostname;
		}
		else if (window.location.host.indexOf("172.16.") != -1) {
			strHost = window.location.hostname;
		}
		else if(window.location.host.indexOf("17u.cn") != -1){
			strHost = ".17u.cn";
		}
		else{
			strHost = "";
		}
		
		//setTimeout(function(){
		    squl.cookie.del(name);
			squl.cookie.set(name, value, null, {path: '/', encode:false});//为了解决360重写会话cookie的bug
			squl.cookie.set(name, value, null, {path: '/', domain:strHost, encode:false});
			squl.cookie.set(name, value, -1, {path: '/', encode:false});//为了解决360重写会话cookie的bug
			squl.cookie.set(name, value, null, {path: '/', domain:strHost, encode:false});
	    //}, 100);	
	
		//setTimeout(function(){
		    squl.cookie.del("17uCNRefId");
			squl.cookie.set("17uCNRefId", refId, null, {path: '/', encode:false});
			squl.cookie.set("17uCNRefId", refId, null, {path: '/', domain:strHost, encode:false});
			squl.cookie.set("17uCNRefId", refId, -1, {path: '/', encode:false});
			squl.cookie.set("17uCNRefId", refId, null, {path: '/', domain:strHost, encode:false});
	    //}, 100);

		//setTimeout(function(){
		    squl.cookie.del("TicketSEInfo");
		    squl.cookie.set("TicketSEInfo", value, null, {path: '/', encode:false});
		    squl.cookie.set("TicketSEInfo", value, null, {path: '/', domain:strHost, encode:false});
		    squl.cookie.set("TicketSEInfo", value, -1, {path: '/', encode:false});
		    squl.cookie.set("TicketSEInfo", value, null, {path: '/', domain:strHost, encode:false});
		//}, 100);
	};
	
	
	function reDumpRefid(data){
		if(data && data.words && data.words !== ""){
			dumpRefid(data.words);
		}
		else{
			dumpRefid("");
		}
	}
	
	function getRefid() {
	    var url = window.location.href.toLowerCase(), refid, dStr;
	    refid = squl.cookie.getChild("CNSEInfo", "RefId");
		
		var dIndex = url.indexOf("#");
		var pIndex = url.indexOf("?");
	    if ( dIndex > -1 ) {
			dStr = url.substring(dIndex + 1);
	       	var rid = findParam(dStr, "refid");
			if(rid !== "" && rid !== "undefined" && rid != undefined){
				refid = rid;
			}
	    }
		
	   	if(pIndex > -1){
			if(dIndex > -1 && pIndex < dIndex){
				dStr = url.substring(pIndex+1, dIndex);
			}
			else{
				dStr = url.substring(pIndex+1);
			}
	       	var rid = findParam(dStr, "refid");
			if(rid !== "" && rid !== "undefined" && rid != undefined){
				refid = rid;
			}		
	    }
		refid = refid == undefined ? "0" : refid;
		refid = refid == "undefined" ? "0" : refid;
	    return refid === "" ? "0" : refid;
	}
	
	function findParam(str, key){
		var strArr = str.split("&");
		for(var n=0; n<strArr.length; n++){
			if( strArr[n].substring(0, key.length + 1) == (key + '=') ){
				return strArr[n].substring(key.length + 1);
			}
		}
		return "";
	}
	
	
	function AnalyseSearchEngine() {
	    var newArr = new Array('','','');
	    if (document.referrer && document.referrer != '') {
	        var refer = document.referrer;
	        var seFrom, encode;
	        if (refer.match(new RegExp('baidu\\.'))) {
	            seFrom = 'baidu';
	            encode = 'gb2312';
	            if (refer.match(new RegExp('(\\?|\\&)(wd|word)\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)(wd|word)\\=([^\\&]+)'));
	                seKeyWords = arr[3];
	                if (refer.match(new RegExp('(\\?|\\&)(ie)\\=utf\\-8'))) {
	                    encode = 'utf-8';
	                }
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	        if (refer.match(new RegExp('google\\.'))) {
	            seFrom = 'google';
	            encode = 'utf-8';
	            if (refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'));
	                seKeyWords = arr[2];
	                if (refer.match(new RegExp('(\\?|\\&)(ie)\\=(gb2312)|(gb)'))) {
	                    encode = 'gb2312';
	                }
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	        if (refer.match(new RegExp('yahoo\\.'))) {
	            seFrom = 'yahoo';
	            encode = 'utf-8';
	            if (refer.match(new RegExp('(\\?|\\&)p\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)p\\=([^\\&]+)'));
	                seKeyWords = arr[2];
	                if (refer.match(new RegExp('(\\?|\\&)(ei)\\=(GBK|gbk)'))) {
	                    encode = 'gbk';
	                }
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	        if (refer.match(new RegExp('bing\\.'))) {
	            seFrom = 'bing';
	            encode = 'utf-8';
	            if (refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'));
	                seKeyWords = arr[2];
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	        if (refer.match(new RegExp('soso\\.'))) {
	            seFrom = 'soso';
	            encode = 'gb2312';
	            if (refer.match(new RegExp('(\\?|\\&)w\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)w\\=([^\\&]+)'));
	                seKeyWords = arr[2];
	                if (refer.match(new RegExp('(\\?|\\&)(ie)\\=(UTF|utf)\\-8'))) {
	                    encode = 'utf-8';
	                }
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	        if (refer.match(new RegExp('sogou\\.'))) {
	            seFrom = 'sogou';
	            encode = 'gb2312';
	            if (refer.match(new RegExp('(\\?|\\&)query\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)query\\=([^\\&]+)'));
	                seKeyWords = arr[2];
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	        if (refer.match(new RegExp('iask\\.'))) {
	            seFrom = 'iask';
	            encode = 'gb2312';
	            if (refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'));
	                seKeyWords = arr[2];
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	        if (refer.match(new RegExp('youdao\\.'))) {
	            seFrom = 'youdao';
	            encode = 'utf-8';
	            if (refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'))) {
	                var arr = refer.match(new RegExp('(\\?|\\&)q\\=([^\\&]+)'));
	                seKeyWords = arr[2];
	                if (refer.match(new RegExp('(\\?|\\&)(ie)\\=(gb2312)|(gb)'))) {
	                    encode = 'gb2312';
	                }
	            }
	            newArr[0] = seFrom;
	            newArr[1] = encode;
	            newArr[2] = seKeyWords;
	            return newArr;
	        }
	    }
	    return newArr;
	}
	
	window.reDumpRefid = reDumpRefid;
	window.getRefid = getRefid;
	window.dumpRefid = dumpRefid;
})();


//----------refid相关----------------------->>

//<<---------- footer img man 页脚同程图片处理 -----------------------
var footImgMan = {
	list: [
		'酒店',
		'机票',
		'景点',
		'租车',
		'旅游度假',
		'团购'
	],
	whichProject: function() {
		if(squl.dom('#header .header_nav_on')[0]){
			var n = squl.dom('#header .header_nav_on')[0].innerHTML;
			var projectNames = footImgMan.list;
			for(var i = 0, l = projectNames.length; i < l; i+=1) {
				if(n.indexOf(projectNames[i]) > -1) {
					return (projectNames[i]);
				}
			}
		}
	},
	preSort: function() {
		if(squl.dom('#footer .footer_img .ft_set_bg')[0]){
			var footImgsWrap = squl.dom('#footer .footer_img .ft_set_bg');
			var footImgs = squl.dom('a', footImgsWrap);
			footImgsWrap.appendChild(footImgs[2], footImgs[3]);
			return squl.dom('a', footImgsWrap);
		}
	},
	process: function(project) {
		if(project){
			var imgsWillChange = footImgMan.preSort();
			function modifyImgAndHref(projectImgName) {
				squl.setStyle({background: 'url(http://img1.40017.cn/cn/new_ui/public/images/' + projectImgName + '5.png) no-repeat 0 0'}, imgsWillChange[4]);
				squl.setStyle({background: 'url(http://img1.40017.cn/cn/new_ui/public/images/' + projectImgName + '6.png) no-repeat 0 0'}, imgsWillChange[5]);
			}
			switch(project) {
				case '酒店':
					modifyImgAndHref('hotel');
					imgsWillChange[4].href = imgsWillChange[5].href = 'http://www.17u.cn/subject/sidabaozhang/';
					break;
				case '机票':
					modifyImgAndHref('flight');
					imgsWillChange[4].href = 'http://www.17u.cn/flight/zt/catazizhi.aspx';
					imgsWillChange[5].href = 'http://www.17u.cn/flight/zt/dijiabaozhang.html';
					break;
				case '景点':
					// to process
					break;
				case '租车':
					// to process
					break;
				case '旅游度假':
					// to process
					break;
				case '团购':
					// to process
					break;
				default: break;
			}
		}
	}
};
squl.on('load', function() {footImgMan.process(footImgMan.whichProject())}, window);
//---------- footer img manager 页脚同程图片处理 ----------------------->>

try
{
	var headerTop = document.getElementById("header");
	if(navigator.userAgent.indexOf("MSIE 6.0")==-1 && headerTop)
	{
		var headerTop_li = headerTop.getElementsByTagName("li");
		
		for(var a=0;a<headerTop_li.length;a++)
		{
			if(headerTop_li[a].className == "")
			{
				headerTop_li[a].onclick = function()
				{
					for (var b = 0; b < headerTop_li.length; b++) 
					{
						if (headerTop_li[b].className == "header_nav_on") 
						{
							headerTop_li[b].className = "";
						}
					}
					this.className = "header_nav_on";
				}
			}
		}
	}
}
catch(e)
{
	
}

//-----登录/400/公告，模块状态更新----
function getLoginInfoCallback(data) {
    function get(id) {
        return document.getElementById(id);
    }
    //设置400电话
    function setTelephone(num){
        var proj = window._tcProject,
            proj400,
            subData,
            enable = true,
            type = "big",
            refid = getRefid();
        //默认使用其他项目的设置
        if(!proj){
            proj = "other";
        }
        //默认大电话，全部显示
        if(window._tc400data && window._tc400data[proj]){
            proj400 = window._tc400data[proj];
        }
        else if(window._tc400data && !window._tc400data[proj] && window._tc400data.other){
            proj400 = window._tc400data.other;
        }
        else{
            proj400 = { except: "big"};
        }

        //refid判断
        if(proj400.include){
            var catchit = false;
            for(var n=0; n<proj400.include.length; n++){
                subData = proj400.include[n];
                if(subData && subData.channel){
                    for(var i=0; i<subData.channel.length; i++){
                        if(subData.channel[i] == refid){
                            type = subData.type;
                            catchit = true;
                        }
                    }
                }

            }
            if(!catchit){
                type = proj400.except;
            }
        }else{
            type = proj400.except;
        }



        if(enable){
            switch(type){
                case "none":
                    break;
                case "little":
                    get("littletelephone_outer") && (get("littletelephone_outer").style.display = "inline");
                    get("littletelephone") && (get("littletelephone").innerHTML = num);
                    break;
                default:
                    get("headtelephone") && (get("headtelephone").innerHTML = num);
                    get("hotline_holder") && (get("hotline_holder").style.display = "block");
                    break;
            }

        }
        get("foottelephone") && (get("foottelephone").innerHTML = num);
    }
    if (data) {
        switch (data.state) {
            case 100:
                //更换头部登录状态
                loginState = data;
                get("user_name") && (get("user_name").innerHTML = data.username);
                get("login_bar") && (get("login_bar").style.display = "none");
                get("logged_bar") && (get("logged_bar").style.display = "block");
                get("loginBar") && (get("loginBar").style.display = "none");
                get("logoutBar") && (get("logoutBar").style.display = "block");
                window.onTcLoginSuccess && window.onTcLoginSuccess(data);
                break;
            default:
                window.onTcLoginError && window.onTcLoginError(data);
                break;
        }
        get("bulletin") && (get("bulletin").innerHTML = data.bulletin);
        //400不同状态
        if(data.telephone){
            setTelephone(data.telephone)
        }
    }
    window.loginStateReady && window.loginStateReady();
}
//-----登录/400/公告，模块状态更新----
