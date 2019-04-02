<link rel="stylesheet" type="text/css" href="__TAG__/tag.css"/>
<style>
.panel-active.selector-panel{overflow-y: scroll; height: 240px;}
</style>
<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';

if(!$value && @$param.default_value){
	$value = $this->input->get_true_value($param.default_value);
}
if(strpos($param.state,'=')>0)
{
	$this->{'input.tree_selector.helper'}->set_state($param.state,$param.state);
}
?>
<!--<div class="input-group form-control tree-selector-zhang-input-group" style="padding:0;">-->
<div class="input-group tree-selector-zhang-input-group" style="padding:0;">
	<div class="input-group-long" style="width:350px;padding:6px 12px;">
		<input type="hidden" name="{$param.name}" value="{$value}" />
		<input type="text"
			pointer="{$param.name}"
			class="input_select_printer"
			from_table="{$param.from_table}"
			value_column="{$param.value_column}"
			show_column="{$param.show_column}"
			fid_column="{$param.fid_column}"
			state="<?php echo $this->view->get_var($this->input->get_true_value($param.state));?>"
			id="input_select{$input_select}{$ts_flag}"
			max_selection = "{$param.max_selection}"
			showname="{$param.showname}"
			style="border:0;width:100%;outline:none;"
			inited="false"
			readonly="" 
		/>
	</div>  
</div>
<div class="tree-selector-zhang" onselectstart="return false;" style="display:none;height:0;">
	<div class="tree-selector-zhang-true">
		<div class="tszt-header text-right">
			<div class="left">
				<label><span>全部</span> &gt; </label>
			</div>
			<input type="button" class="btn btn-info btn-xs" value="关闭" />
		</div>
		<div class="tszt-body">
		</div>
	</div>
</div>
<?php
if(!isset($tree_selector_zhang))
{
$tree_selector_zhang=true;
?>
<script>
if(typeof $.fn.mCustomScrollbar =='undefined')
{
	$('<script src="__TAG__/jquery.mCustomScrollbar.concat.min.js"><' +'/script>').appendTo($('head'));
	$('<link rel="stylesheet" type="text/css" href="__TAG__/jquery.mCustomScrollbar.min.css"/>').appendTo($('head'));
}
// function init(panel_id)
// 	{
// 		if($('#'+panel_id).size()==0){
// 			var selector_id = panel_id.replace(/panel$/ig,'');
// 			var selector = $('#'+selector_id);
// 			var modal = $('.tree-selector-modal:eq(0)').clone().removeClass('tree-selector-modal').addClass('tree-selector');
// 			modal.attr('id',panel_id);
// 			modal.appendTo($('body'));
// 			modal.find('button.close').unbind().click(function()
// 			{
// 				modal.modal('hide');
// 			});
			
// 			if(modal.find('.modal-body').size()>0){
// 				modal.find('.modal-title').html(selector.attr('showname'));
// 				modal.find('.modal-body').attr('selector',selector_id);
// 				modal.find('.modal-body').html('<div class="selector"></div><div class="result"></div>');
// 			}else{
// 				modal.drawer({position:'right',showIcon:true,background:'white'});
// 				modal.find('.drawer-content').html('<div class="selector"></div><div class="result"></div>');
// 				modal.find('.drawer-content').attr('selector',selector_id);
				
// 				modal.find('.selector').css({
// 				'max-height':'none',
// 				'-webkit-overflow-scrolling' : 'touch',
// 				'-webkit-box-sizing' : 'border-box'
// 				}).height($(window).height()- 36 - 80);
// 				modal.show();
// 			}
	
// 			var value_container = selector.prev();
// 			var values;
// 			if(value_container.val().trim()=='')
// 			{
// 				values=new Array();
// 			}else{
// 				values  = value_container.val().split(',');
// 			}
			
// 			if(selector.val()=='')
// 			{
// 				return;
// 			}
// 			var htmls = selector.val().split(',');
// 			for(var i in values)
// 			{
// 				var label_html = htmls[i];
// 				var label_value = values[i];
// 				if(label_value=='') continue;
// 				var label=$('<label vl="'+label_value+'"><span class="glyphicon glyphicon-remove"></span>'+label_html+'</label>');
// 				label.appendTo(modal.find('.result'));
// 				label.find('span').click(remove_label);
// 			}
// 		}
// 	}
	
$(function(){
	__page__.find('.tree-selector-zhang-input-group').each(function(tszi){
		var trigger = $(this);
		var selector = trigger.next();
		var timer = null;
		trigger.find('input:text').click(function(){
			selector.show();
			if(selector.find('.selector-panel').size()==0){
				selector.find('.tree-selector-zhang-true').height(0).animate({height:282},function(){
					selector_active(0);
				});
			}
		}).each(function(i)
		{
			var value = $(this).prev().val();
			var selector = $(this);
			var from_table=selector.attr('from_table');
			var value_column=selector.attr('value_column');
			var show_column=selector.attr('show_column');
			var fid_column=selector.attr('fid_column');
			var state=selector.attr('state');
			
			$.get('https://{$_SERVER.HTTP_HOST}__PHP__?class={input:tree_selector_zhang}&method=get_default_show&value='+value+'&from_table='+from_table+'&show_column='+show_column+'&value_column='+value_column+'&fid_column='+fid_column+'&state='+encodeURIComponent(state),function(data,textStatus)
			{
				var panel_id=selector.attr('id')+'panel';
				selector.val(data);

				//init(panel_id);
				
				{if isset($ajax_update)}
				  selector.click();
				{/if}
			});
			{if isset($ajax_update)}selector.css('max-width',120);{/if}
			$(this).attr('inited','true');
		});
		selector.find('input:button').click(function(){
			selector.hide();
		});
		function header_label_click(){
			var index = $(this).parent().find('label').index($(this));
			selector.find('.selector-panel').each(function(i){
				if(i>index)
				{
					$(this).find('li.selected span').trigger('delete-event');
					$(this).remove();
				}else if(i==index)
				{
					var width = selector.find('.tszt-body').width()-index*100;
					$(this).find('li.active').removeClass('active');
					$(this).animate({'width':width}).addClass('panel-active');
				}
			});
			$(this).parent().find('label').each(function(i){
				if(i>index)
				{
					$(this).remove();
				}
			});
		}
		function header_label_hover()
		{
			$(this).addClass('hover');
		}
		function header_label_out()
		{
			$(this).removeClass('hover');
		}
		selector.find('.tszt-header label').click(header_label_click).mouseover(header_label_hover).mouseout(header_label_out);

		function selector_active(fid)
		{
			if(selector.find('.panel-active').size()>0)
			{
				selector.find('.panel-active').mCustomScrollbar({
					axis : 'y',
					scrollButtons:{enable:true},
					theme:"dark-thin",
					scrollbarPosition:"inside"
				}).animate({width:100,height:240},function(){
					$(this).removeClass('panel-active');
					$(this).mCustomScrollbar('scrollTo',$(this).find('li.active'));
					selector_load(fid);
				});
			}else{
				selector_load(fid);
			}
		}

		function selector_load(fid)
		{
			var panel = $('<div class="panel-active selector-panel"><div class="text-center pt100"><base:loading /></div></div>');
			panel.width(selector.find('.tszt-body').width() - selector.find('.selector-panel').size()*100);
			panel.appendTo(selector.find('.tszt-body'));

			var from_table=trigger.find('input:text').attr('from_table');
			var value_column=trigger.find('input:text').attr('value_column');
			var show_column=trigger.find('input:text').attr('show_column');
			var fid_column=trigger.find('input:text').attr('fid_column');
			var state = encodeURIComponent(trigger.find('input:text').attr('state'));
			var value = selector.prev().val();
			var url = 'https://{$_SERVER.HTTP_HOST}__PHP__?class={input:tree_selector_zhang}&from_table='+from_table+'&show_column='+show_column+'&value_column='+value_column+'&fid_column='+fid_column+'&fid='+fid+'&state='+state+'&value='+value+'&multi_parent={$param.multi_parent}';
			

			$.get(url,function(data,textStatus){
				panel.html(data);
				panel.find('li').dblclick(function(){
					timer && clearTimeout(timer);
					if($(this).find('span.glyphicon-triangle-right').size()==1 && !$(this).hasClass('active'))
					{
						if(!$(this).parent().parent().hasClass('panel-active'))
						{
							var _panel=$(this).parent().parent().parent().parent();
							var index=_panel.parent().find('.selector-panel').index(_panel);
							_panel.parent().find('.selector-panel').each(function(i){
								if(i>index)
								{
									$(this).find('li.selected span').trigger('delete-event');
									$(this).remove();
								}
							});

							selector.find('.tszt-header label').each(function(i){
								if(i>index)
								{
									$(this).remove();
								}
							});
						}

						//插入导航
						var label = '<label><span>'+$(this).find('label').html()+'</span> &gt; </label>';
						label=$(label);
						label.appendTo(selector.find('.tszt-header div'));
						label.click(header_label_click).mouseover(header_label_hover).mouseout(header_label_out);


						$(this).parent().find('li.active').removeClass('active');
						var data_id = $(this).attr('data_id');
						selector_active(data_id);
						$(this).addClass('active');
					}
				}).click(select_label);
				panel.find('li span').bind('delete-event',remove_label);
			});
		}
		function select_label()
		{
			var li = $(this);
			timer && clearTimeout(timer); 
			timer = setTimeout(function(){
				if(li.hasClass('selected'))
				{
					li.find('span').trigger('delete-event');
					li.removeClass('selected');
					return;
				}
				var label_html = li.find('label').html();
				var label_value = li.attr('vl');

				if(label_value==''){
					li.find('input:eq(0)').prop('checked',false);
					return;
				}
				
				
				var parent=li.parent();
				//while(!parent.is('div')) parent=parent.parent();
				if(li.find('span').hasClass('glyphicon') && trigger.find('input:text').attr('final_only')=='1')
				{
					alert('只能选择无子数据的');
					return;
				}
				
				var value_container = trigger.find('input:eq(0)');
				var values;
				if(value_container.val().trim()=='' || value_container.val()==0)
				{
					values=new Array();
				}else{
					values  = value_container.val().split(',');
				}
				var exists = false;
				for(var i in values)
				{
					var value = values[i];
					if(value!='' && value==label_value)
					{
						exists = true;
						break;
					}
				}
				
				if(!exists)
				{
					if(trigger.find('input:text').attr('max_selection') == 1)
					{
						selector.find('li.selected span').each(function(i)
						{
							$(this).trigger('delete-event');
						});
					}
					
					if(trigger.find('input:text').attr('max_selection')>1 && values.length == trigger.find('input:text').attr('max_selection'))
					{
						li.find('input:eq(0)').prop('checked',false);
						alert('最多只能选择'+trigger.find('input:text').attr('max_selection')+'个选项');
						return;
					}
					if(values.length==0 || value_container.val()=='')
					{
						value_container.val(label_value);
						trigger.find('input:text').val(label_html);
					}else{
						value_container.val(value_container.val()+','+label_value);
						trigger.find('input:text').val(trigger.find('input:text').val()+','+label_html);
					}
					
					value_container.change();
				
					li.find('input').prop('checked',true);
				}
				li.addClass('selected');
			},300);
		}
		function remove_label()
		{
			var label = $(this).parent();
			var label_value = label.attr('vl');

			
			var panel=label.parent().parent();
			
			//var selector=$('#'+panel.attr('selector'));
			
			var value_container = trigger.find('input:eq(0)');
			var values;
			if(value_container.val().trim()=='')
			{
				values=new Array();
			}else{
				values  = value_container.val().split(',');
			}
			var htmls = trigger.find('input:text').val().split(',');
			
			var new_value = '';
			var new_html = '';
			for(var i in values)
			{
				var value = values[i];
				var html = htmls[i];
				if(value!=label_value)
				{
					if(new_value!='') new_value+=',';
					new_value += value;
					
					if(new_html!='') new_html+=',';
					new_html+=html;
				}
			}
			
			trigger.find('input:text').val(new_html);
			value_container.val(new_value);
			label.removeClass('selected');
		}
	});
	//var trigger = $('.tree-selector-zhang-input-group');

});
</script>
<?php }?>