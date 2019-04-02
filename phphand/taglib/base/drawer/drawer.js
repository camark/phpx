
;$.fn.extend({
	drawer : function(arg)
	{
		var box = $(this);
		if(typeof arg=='object')
		{
			box.css({position:'fixed',top:0,bottom:0,overflow:'auto'});
			if(typeof arg.width!='undefined')
			{
				if(arg.width=='wide')
					box.width($(window).width()-50);
				else
					box.width(arg.width);
			}else{
				box.width($(window).width());
			}

			if(typeof arg.position!='undefined' && arg.position=='right')
			{
				box.css({right:0-box.width()});
				box.attr('position','right');
			}else{
				box.css({left:0-box.width()});
				box.attr('position','left');
			}

			if(typeof arg.background!='undefined')
			{
				box.css({background:arg.background});
			}else{
				box.css({background:'#000'});
			}
			
			if(typeof arg.showIcon!='undefined' && arg.showIcon==true)
			{
				var html = '<div style="padding:7px;" class="drawer-head"><span style="background:#555;opacity:0.6;color:#fff;font-size:18px;" class="glyphicon glyphicon-remove"></span></div>';
				html +='<div class="drawer-content" style="padding:0;"></div>';
				box.html(html);
				box.find('.drawer-head span').click(function()
				{
					box.drawer('hide');
				});
			}
			
			touch.on('#'+box.attr('id'),'swipe'+box.attr('position'),function()
											  {
												  box.drawer('hide');
											  });
		}else if(typeof arg=='string' && arg=='show')
		{
			if(box.attr('position')=='left'){
				box.animate({left:0},300);
			}else{
				box.animate({right:0},300);
			}
			if(typeof MainContainerDrawerHandle!='undefined')
			{
				MainContainerDrawerHandle(box.attr('position'),box.width(),'hide');
			}
		}else if(typeof arg=='string' && arg=='hide')
		{
			if(box.attr('position')=='left'){
				box.animate({left:0-box.width()},300);
			}else{
				box.animate({right:0-box.width()},300);
			}
			if(typeof MainContainerDrawerHandle!='undefined')
			{
				MainContainerDrawerHandle(box.attr('position'),box.width(),'show');
			}
		}
	}
});


