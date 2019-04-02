// JavaScript Document
$.fn.extend({
	//获取时间戳
	getTimeStamp:function(){
		var times=$(this).val();
		var times_str = times.replace(/\-/g,'/')+' 0:0:0';
		//var arr = new_str.split("-");
		var datum = new Date(times_str);
		var timeint=Math.round(datum.getTime()/1000);
		return timeint;
	}
});