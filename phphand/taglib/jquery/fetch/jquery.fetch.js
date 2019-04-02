var operating=false;
var fore_click=null;
var clicked={};
//已加载的js
var loaded_js=new Array();
	loaded_js[0]='/PHPHand/taglib/jquery/basic/jquery-1.3.2.min.js';

function link_click(obj){
	if($('#box_iframe').size()==0){
		//src="App/View/Default/Public/js/load.html"
		var iframe_html='<iframe border="0" frameborder="0" id="box_iframe" style="width:960px;height:394px;display:none;" />';
		$('body').append($(iframe_html));
	}
	if(operating) return false;
	operation=true;
	if(!('_href' in obj)){
		obj=this;
	}
	//根据当前链接进行界面预处理
	if($(obj).parent().parent().parent().is('.menu-float')){
		$('#nav-menu-tree li.cur').removeClass('cur');
		$(obj).parent().addClass('cur');
	}else if($(obj).parent().parent().parent().is('.small-menu-list')){
		$('.small-menu-list li.absolute').removeClass('absolute');
		$(obj).parent().addClass('absolute');
	}else{
		$('#nav-menu-tree li.cur').removeClass('cur');
		var li=$('<li class="cur" click_flag="o'+new Date().getTime()+'"></li>');
		var clone=$(obj).clone();
		if(clone.html().indexOf('<label>')<0) clone.html('<label>'+clone.html()+'</label>');
		clone.click(link_click);
		li.append(clone);
		li.find('a').append($('<span></span>'));
		li.find('a').append($('<div class="clear"></div>'));
		li.find('span').mouseover(function(){
			$(obj).addClass('hover');
		}).mouseout(function(){
			$(obj).removeClass('hover');
		}).click(function(){
			var click_flag=$(this).parent().parent().attr('click_flag');
			if($(this).parent().parent().hasClass('cur')){
				//eval('var _obj=clicked.'+click_flag+';');
				var _obj=clicked[click_flag];
				link_click(_obj);
			}
			$(this).parent().parent().remove();
		});
		$('#nav-menu-tree ul').append(li);
		clicked[li.attr('click_flag')]=fore_click;
	}
	fore_click=obj;
	var _target=$(obj).attr('_target');
	if(typeof(_target)=="undefined"){
		if(typeof(_target)=="undefined") _target='center';
	}
	var url=$(obj).attr('_href');
	if(url.indexOf('?')>=0){
		url+='&get_ajax_page=true';
	}else{
		url+='?get_ajax_page=true';
	}
	open_link(url,_target);
}
function open_link(url,target,post_data){
	/*
	if($('#app_mask').size()==0){
		$('<div id="app_mask" style="display:none;position:absolute;z-index:900;top:0;left:0;background:#fff;"></div>').appendTo($('body'));
		$('<div id="app_loading" style="display:none;position:absolute;z-index:901;text-align:center;"><div style="width:25px;height:25px;padding:10px;text-align:center;margin:180px auto;"><sui:loading /></div></div>').appendTo($('body'));
		$('#app_mask').css('opacity',0.3);
		//$('#app_loading img').css('margin-top','230px');
	}
	$('#app_mask').css({
		width : $(document)[0].offsetWidth,
		height: $(document)[0].offsetHeight
	}).show();
	$('#app_loading').css({
		top   : $(document).scrollTop()+'px',
		left  : $(document).scrollLeft()+'px',
		width : $('html').width(),
		height: $('html').height()
	}).show();*/
	if(target.match(/^\[[\s\S]+?\]$/ig)){
		url+='&get_ajax_page=true';
	}
	
	if(typeof(post_data)=='undefined'){
		func=$.get;
		post_data='';
	}else{
		func=$.post;
	}
	func(url,post_data,function(data,textStatus){
		if(textStatus=='success'){
			var matches=data.match(/<script[^>]+?src="(.+?)"[^>]*?><\/script>/ig);
			var srcs=new Array();
			for(mt in matches){
				if(mt=='input') continue;
				if(typeof(matches[mt])=='number') break;
				var src=matches[mt].replace(/^[\s\S]+?src="(.+?)"[\s\S]+?$/ig,"$1");
				var loaded=false;
				for(var i=0;i<loaded_js.length;i++){
					if(loaded_js[i]==src){
						loaded=true;
						break;
					}
				}
				if(!loaded){
					srcs[srcs.length]=src;
					loaded_js[loaded_js.length]=src;
				}
				data=data.replace(matches[mt],'');
			}
			
			if(srcs.length==0){
				dump_html(data,target);
			}else{
				script_queue(0,srcs,data,target);
			}
		}
	});
}
function script_queue(i,srcs,data,target){
	$.getScript(srcs[i],function(response,status){
		if(status=='success'){
			if(i==srcs.length-1){
				data=data.replace(/(<script[^>]+?>)\s*?<!\-\-/ig,"$1").replace(/\/\/\-\->\s*?<\/script>/ig,'<'+'/script>');
				dump_html(data,target);
			}else{
				i++;
				script_queue(i,srcs,data,target);
			}
		}
	});
}
function dump_html(data,target){
	if(target.match(/^\[[\s\S]+?\]$/ig)){
		if($('#sys_widget_dialog').size()==0){
			$('<div id="sys_widget_dialog"></div>')
				.appendTo($('body'))
				.dialog({
					auto : false,
					modal : false
				});
		}
		var sys_widget_dialog=$('#sys_widget_dialog');
		var array=target.replace(/^\[([\s\S]+?)\]$/ig,"$1").split(':');
		sys_widget_dialog.dialog('option','title',array[0]);
		switch(array.length){
			case 1:
				sys_widget_dialog.dialog('option','width',400);
				sys_widget_dialog.dialog('option','height',400);
				break;
			case 2:
				sys_widget_dialog.dialog('option','width',array[1]);
				sys_widget_dialog.dialog('option','height',400);
				break;
			default:
				sys_widget_dialog.dialog('option','width',array[1]);
				sys_widget_dialog.dialog('option','height',array[2]);
				break;
		}
		sys_widget_dialog.dialog('option','position',['center','center']);
		sys_widget_dialog.html(data);
		sys_widget_dialog.dialog('open');
	}else{
		$('#'+target).html(data);
	}
	init_link_action($('#'+target));
	//init_data_table($('#'+target));
	//init_button_list($('#'+target));
	operating=false;
	/*$('#app_mask').hide();
	$('#app_loading').hide();*/
}
function init_link_action(container){
	if(container.find('a[_target!='']').size()>0){
		container.find('a[_target!='']').each(function(i){
			$(this).attr('_href',$(this).attr('href'));
			$(this)
			.removeAttr('href')
			.css('cursor','pointer')
			.click(link_click);
		});
	}
}
$(function(){
		   init_link_action($('body'));
		   });
