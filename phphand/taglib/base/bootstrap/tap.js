$(document).on("touchstart",function(e) {
    if (!$(e.target).hasClass("disable"))
    {
    	var startX = parseInt(e.originalEvent.targetTouches[0].pageX);
    	var startY = parseInt(e.originalEvent.targetTouches[0].pageY);
    	$(e.target).data('touchStartX',startX);
    	$(e.target).data('touchStartY',startY);
    	$(e.target).data("isMoved", 0);
    }
});
$(document).on("touchmove",function(e) {
    if(!$(e.target).hasClass("disable")){
	    var nowX;
	    if(typeof e.originalEvent.targetTouches=='undefined' || typeof e.originalEvent.targetTouches[0]=='undefined')
	        nowX = parseInt(e.originalEvent.changedTouches[0].pageX);
	    else
	        nowX = parseInt(e.originalEvent.targetTouches[0].pageX);
	    var nowY;
	    if(typeof e.originalEvent.targetTouches=='undefined' || typeof e.originalEvent.targetTouches[0]=='undefined')
	        nowY = parseInt(e.originalEvent.changedTouches[0].pageY);
	    else
	        nowY = parseInt(e.originalEvent.targetTouches[0].pageY);

	    var moveX = nowX-parseInt($(e.target).data('touchStartX'));
	    var moveY = moveY-parseInt($(e.target).data('touchStartY'));
	   	$(e.target).data("isMoved", 1);
    	$(e.target).data('moveX',moveX);
    	$(e.target).data('moveY',moveY);
    	$(e.target).trigger('swap');
    }
});
$(document).on("touchend",function(e) {
    if (!$(e.target).hasClass("disable") && $(e.target).data("isMoved") == 0) $(e.target).trigger("tap");
});