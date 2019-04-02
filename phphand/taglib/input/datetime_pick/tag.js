$.fn.extend({
	mdatepick : function()
	{
		$(this).click(function(){
			var default_value;
			var text = $(this);
			if($(this).val()!='')
			{
				var array1 = $(this).val().split(' ');
				var array = array1[0].split('-');
				var array2
				if(array1.length==1)
				{
					array2 = [new Date().getHours()+1,30];
				}else{
					array2 = array1[1].split(':');
				}

				default_value = new Date(array[0],parseInt(array[1])-1,array[2],array2[0],array2[1],0);
			}else{
				default_value = new Date();
			}

			var default_stamp = default_value.getTime()/1000;

			var default_date = default_value.getFullYear()+'-'+(default_value.getMonth()+1)+'-'+default_value.getDate();
			var default_min = default_value.getMinutes();
			if(default_min==0) default_min='00';
			var default_time = default_value.getHours()+':'+default_min;

			if($('div.mdatepicker').size()==1)
			{
				$('div.mdatepicker').remove();
			}
			var div = '<div class="mdatepicker container">';
			div += '<div class="row">';
			div += '<div class="mpart col-md-6 col-sm-6 col-xs-6">';
			div += '<div class="scroll">';
			for(var i=-30;i<30;i++)
			{
				var stamp = default_stamp+i*24*3600;
				var date = new Date();
				date.setTime(stamp*1000);
				var str = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();
				div += '<div class="item ditem';
				if(str==default_date) div+=' current';
				div += '" date="'+str+'">'+str+'</div>';
			}
			div += '</div>';
			div += '</div>';
			div += '<div class="mpart col-md-6 col-sm-6 col-xs-6">';
			div += '<div class="scroll">';
			for(var i=0;i<48;i++)
			{
				var min = i%2*30;
				if(min==0) min='00';
				var str = parseInt(i/2)+':'+min;
				div += '<div class="item ditem';
				if(str==default_time) div+=' current';
				div += '" date="'+str+'">'+str+'</div>';
			}
			div += '</div>';
			div += '</div>';
			div += '</div>';
			div += '<div class="row pt10">';
			div += '<div class="col-md-2 col-sm-2 col-xs-2"></div>';
			div += '<div class="col-md-4 col-sm-4 col-xs-4"><input type="button" class="btn btn-default btn-block" value="删除" /></div>';
			div += '<div class="col-md-4 col-sm-4 col-xs-4"><input type="button" class="btn btn-primary btn-block" value="应用" /></div>';
			div += '<div class="col-md-2 col-sm-2 col-xs-2"></div>';

			div += '</div>';
			div += '</div>';
			var div = $(div);
			div.appendTo($('body'));

			div.find('.scroll').bind('touchstart',function(e){
				var startY = parseInt(e.originalEvent.targetTouches[0].pageY);
				$(this).data('startY',startY);
				$(this).data('marginY',parseInt($(this).css('marginTop')));
			});
			div.find('.scroll').bind('touchmove',function(e){
				var nowY;
			    if(typeof e.originalEvent.targetTouches=='undefined' || typeof e.originalEvent.targetTouches[0]=='undefined')
			        nowY = parseInt(e.originalEvent.changedTouches[0].pageY);
			    else
			        nowY = parseInt(e.originalEvent.targetTouches[0].pageY);
			    var startY = parseInt($(this).data('startY'));
			    $(this).data('moveY',nowY-startY);

			    var nowMargin = $(this).data('marginY') + $(this).data('moveY');
			    $(this).css('marginTop',nowMargin);

			    $(this).find('.item').removeClass('current');

			    var n;
			    var an = parseInt(nowMargin / 25)+1;
			    //就是被遮挡的数量
			    //0 - 2
			    //-1 - 3
			    //-2 - 4
			    //-3 - 5
			    //-4 - 6
			    //-5 - 7

			    //1 - 1
			    //2 - 0
			    if(nowMargin / 25 > -1 && nowMargin / 25<=0.5){
			    	n = 2;
			    }else if(an==1)
			    {
			    	n=1;
			    }else if(an>=2)
			    {
			    	n=0;
			    }else if(an<=3-$(this).find('.item').size())
			    {
			    	n = $(this).find('.item').size()-1;
			    }else{
			    	n = Math.abs(an) +3;
			    }
			    
			    console.log($(this).find('.item').size(),an,n);
			    

			    $(this).data('n',n);

			    $(this).find('.item:eq('+n+')').addClass('current');

			});
			div.find('.scroll').bind('touchend',function(e){
				var n = $(this).data('n');

				$(this).animate({'margin-top':(2-n)*25});
			});

			div.find('.scroll:eq(1)').data('n',div.find('.scroll:eq(1) .item').index(div.find('.scroll:eq(1) .current')));
			div.find('.scroll:eq(1)').trigger('touchend');


			div.find('.scroll:eq(0)').data('n',div.find('.scroll:eq(0) .item').index(div.find('.scroll:eq(0) .current')));
			div.find('.scroll:eq(0)').trigger('touchend');

			div.find('input:eq(0)').click(function(){
				text.val('');
				div.remove();
			});

			div.find('input:eq(1)').click(function(){
				if(div.find('.scroll:eq(0) .current').size()==0)
				{
					alert('请选择日期');
					return;
				}
				if(div.find('.scroll:eq(1) .current').size()==0)
				{
					alert('请选择时间');
					return;
				}
				text.val(div.find('.scroll:eq(0) .current').attr('date')+' '+div.find('.scroll:eq(1) .current').attr('date'));
				div.remove();
			});
		});

		
	}
});