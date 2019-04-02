if(typeof $.fn.zh_resizable=='undefined')
{
	$.fn.extend({
		zh_resizable : function(__server)
		{
			var table = $(this);
			var thead = table.find('thead');
			var helper = $('<div class="arrange-table-helper" style="border:0 solid #d00;position:absolute;z-index:200;opacity:0.8;cursor:move;" onselectstart="return false;" style="-moz-user-select:none;"></div>');
			helper.appendTo($('body')).hide();
			
			
			
			function arrange_up_handle(e)
			{
				var td = null;
				thead.find('td:visible').each(function(i)
				{
				       if(parseInt($(this).offset().left)<e.pageX && parseInt($(this).offset().left)+parseInt($(this).css('width'))>=e.pageX)
				       {
					       td = $(this);
					       return false;
				       }
				});
				table.attr('arrange','off');
				table.find('td').removeClass('left_line_shine').removeClass('right_line_shine');
				helper.unbind();
				helper.hide();
				var on_index = thead.find('td:visible').index(td);
				if(parseInt(e.pageX)<parseInt(td.offset().left) + parseInt(td.css('width'))/2)
				{
					on_index --;
				}
				var index = parseInt(table.attr('arrange_index'));
				
				if(on_index>=index) on_index++;
				
				var true_index = parseInt(table.attr('arrange_true_index'));
				table.find('tr').each(function(i)
			       {
				       $(this).find('td:eq('+true_index+')').show();
			       });
				
				if(on_index!=index){
					table.find('tr').each(function(i){
						var tr = $(this);
						var target = tr.find('td:visible').eq(on_index);
						tr.find('td:visible:eq('+index+')').insertAfter(target);
					});
					
					//保存排序
					var post={};
					table.find('thead td').each(function(i){
						var td = $(this);
						if(typeof td.attr('field')!='undefined')
						{
							post['arrange_order_'+td.attr('field')]=i;
						}
					});
					$.post('?class={output:table}&method=save_list_config&config='+table.attr('config'),post);
				}
			}
			
			function helper_move_handle(e)
			{
				helper.css('left',e.pageX);
				
				thead.find('td:visible').each(function(i)
				{
					var td = $(this);
					var on_index = thead.find('td:visible').index(td);
				       if(parseInt(td.offset().left)<e.pageX && parseInt(td.offset().left)+parseInt(td.css('width'))>=e.pageX)
				       {
					       if(parseInt(e.pageX)<parseInt(td.offset().left) + parseInt(td.css('width'))/2){
						       table.find('tr').each(function(j){
							      $(this).find('td:visible').eq(on_index).addClass('left_line_shine').removeClass('right_line_shine');
						      });
					       }else{
						       table.find('tr').each(function(j){
							      $(this).find('td:visible').eq(on_index).removeClass('left_line_shine').addClass('right_line_shine');
						      });
					       }
				       }else{
					       table.find('tr').each(function(j){
						      $(this).find('td:visible').eq(on_index).removeClass('left_line_shine').removeClass('right_line_shine');
					      });
				       }
				});
			}
			
			function resize_up_handle(e)
			{
				//保存宽度
				var post={};
				table.find('thead td').each(function(i){
					var td = $(this);
					if(typeof td.attr('field')!='undefined')
					{
						post['width_'+td.attr('field')]=parseInt($(this).css('width'));
					}
				});
				$.post('?class={output:table}&method=save_list_config&config='+table.attr('config'),post);
			}
			
			//if(td.is(td.parent().find('td:first'))) return;
			$(this).mousemove(function(e)
			{
				if($(this).attr('resize')=='on')
				{
					var moved = parseInt(e.pageX) - parseInt(table.attr('resize_start_page_x'));
					
					var index = parseInt(table.attr('resize_index'));
					
					var width = parseInt(table.attr('resize_start_width'))-moved;
					var width_prev = parseInt(table.attr('resize_start_prev_width'))+moved;
					
					
					table.find('tr').each(function(i){
						var tr = $(this);
						tr.find('td:visible:eq('+index+')').css('width',width);
						tr.find('td:visible:eq('+(index-1)+')').css('width',width_prev);
					});
				}else if(table.attr('arrange')=='on' && table.attr('arrange_moved')=='yes')
				{
					helper_move_handle(e);
				}
			});
			
			
			thead.find('td').mousedown(function(e){
				var index = $(this).parent().find('td:visible').index($(this));
				if(index==0) return;
				if(e.pageX < $(this).offset().left + 5)
				{
					table.attr('resize','on');
					table.attr('resize_index',index);
					table.attr('resize_start_page_x',e.pageX);
					table.attr('resize_start_width',parseInt($(this).css('width')));
					table.attr('resize_start_prev_width',parseInt($(this).parent().find('td:visible:eq('+(index-1)+')').css('width')));
				}else if(e.pageX > $(this).offset().left + parseInt($(this).css('width'))-5){
					table.attr('resize','on');
					table.attr('resize_index',index+1);
					table.attr('resize_start_page_x',e.pageX);
					var next = $(this).parent().find('td:visible:eq('+(index+1)+')');
					if(next.size()==1){
						table.attr('resize_start_width',parseInt(next.css('width')));
					}else{
						table.attr('resize_start_width',0);
					}
					table.attr('resize_start_prev_width',parseInt($(this).css('width')));
				}else{
					table.attr('arrange','on');
					table.attr('arrange_index',index);
					table.attr('arrange_moved','no');
					thead.css('cursor','move');
					table.attr('arrange_start_page_x',e.pageX);
					table.attr('arrange_true_index',table.find('thead').find('td').index($(this)));
				}
			}).mouseup(function(e){
				if(table.attr('resize')=='on')
				{
					table.attr('resize','off');
					resize_up_handle(e);
				}else if(table.attr('arrange')=='on'){
					if(table.attr('arrange_moved')=='yes'){
						arrange_up_handle(e);
					}else{
						table.attr('arrange','off');
					}
				}
			}).mousemove(function(e)
			{
				if(table.attr('arrange')=='on' && table.attr('arrange_moved')=='no' && Math.abs(parseInt(e.pageX) - parseInt(table.attr('arrange_start_page_x')))>4)
				{
					thead.find('td').css('cursor','move');
					console.log('helper show');
					helper.css('top',table.offset().top);
					helper.height(table.height());
					helper.css('left',e.pageX);
					helper.show();
					helper.mouseup(arrange_up_handle).mousemove(helper_move_handle);
					
					var index = parseInt(table.attr('arrange_index'));
					
					
					
					var html = '<table class="data">';
					html += '<thead><td>'+$(this).html()+'</td></thead><tbody>';
					table.find('tbody tr').each(function(i)
					{
						var td = $(this).find('td:visible').eq(index);
						html += '<tr><td>'+td.html()+'&nbsp;</td></tr>';
					});
					html += '</tbody></table>';
					helper.html(html);
					var _table = helper.find('table');
					_table.width(parseInt($(this).css('width')));
					_table.find('tbody td').css('min-height','35px');
					
					table.find('tr').each(function(x)
				       {
					       $(this).find('td:visible').eq(index).hide();
				       });
					
					table.attr('arrange_moved','yes');
				}else if(e.pageX < $(this).offset().left + 5 || table.attr('resize')=='on' || e.pageX > $(this).offset().left + parseInt($(this).css('width'))-5)
				{
					thead.find('td').css('cursor','e-resize');
				}else{
					thead.find('td').css('cursor','pointer');
				}
				
			});
		}
	});
}