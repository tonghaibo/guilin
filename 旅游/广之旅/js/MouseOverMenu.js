$(document).ready(function(){
	var objStr = ".MouseOverMenu";
	$(objStr).mouseover(function(){$(objStr + " ol").show();});
	$(objStr).mouseout(function(){$(objStr + " ol").hide();});
});