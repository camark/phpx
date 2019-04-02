<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';

if(!$value && @$param.default_value){
	$value = $this->input->get_true_value($param.default_value);
}
$ts_flag=time() . rand(1000,9999);
?>
{if !isset($text_select)}
<?php $text_select=0;?><link rel="stylesheet" type="text/css" href="__TAG__/tag.css?" />
{else}
<?php $text_select++;?>
{/if}
<div class="input-group form-control text-select-input-group" style="padding:0;">
	<div class="text-select-input-group-long" style="padding:6px 8px;">
		<input type="hidden" name="prefix${$param.name}" value="AND" />
		<input type="text"
			pointer="{$param.name}"
			class="text_select_printer"
			from_table="{$param.from_table}"
			value_column="{$param.value_column}"
			key_source="{$param.key_source}"
			state="{$param.state}"
			id="text_select{$text_select}{$ts_flag}"
			max_selection = "{$param.max_selection}"
			showname="{$param.showname}"
			style="border:0;width:2000px;outline:none;"
			inited="false"
		/>
	</div>
</div>
<div class="text-select" style="display:none;">
	<div class="text-select-true">
	</div>
</div>
<script type="text/javascript"><!--
$(function()
{
	var page=__page__;
	var interval = null;
	var worker = null;
	var prev_value = '';
	page.find('.text-select').each(function(i)
	{
		$(this).css('width',$(this).prev().css('width'));
		$(this).prev().find('input:text').focus(function()
		{
			worker = $(this);
			interval = setInterval(robot,100);
			prev_value = $(this).val();
		}).blur(function()
		{
			clearInterval(interval);
			prev_value = '';
		}).keydown(function(e)
		{
			switch(e.which)
			{
				case 40:
					if(worker.parent().parent().next().find('li.hover').size()==0)
					{
						worker.parent().parent().next().find('li:eq(0)').addClass('hover');
					}else{
						var li = worker.parent().parent().next().find('li.hover');
						if(li.is(li.parent().find('li:last')))
						{
						}else{
							li.removeClass('hover');
							li.next().addClass('hover');
						}
					}
					break;
				case 38:
					if(worker.parent().parent().next().find('li.hover').size()==0)
					{
						worker.parent().parent().next().find('li:last').addClass('hover');
					}else{
						var li = worker.parent().parent().next().find('li.hover');
						if(li.is(li.parent().find('li:first')))
						{
						}else{
							li.removeClass('hover');
							li.prev().addClass('hover');
						}
					}
					break;
				case 13:
					if(worker.parent().parent().next().find('li.hover').size()==1)
					{
						worker.parent().parent().next().find('li.hover').click();
					}else if($(this).val()=='')
					{
						if($(this).parent().find('label').size()==0)
						{
							return false;
						}
						return true;
					}else{
						worker = $(this);
						generate_key();
					}
					return false;
				case 32:
					if($(this).val()==' ')
					{
						$(this).val('');
						$(this).focus();
						return false;
					}
					worker = $(this);
					generate_key();
					return false;
				case 8:
					if($(this).val()=='')
					{
						remove_last_label($(this).parent());
					}
					break;
			}
		});
	});
	 
	function remove_last_label(container)
	{
		var label = container.find('label:last');
		//label.animate({width:0},function(){
			label.remove();
		//});
	}
	
	function generate_key()
	{
		var theValue = worker.val();
		if(worker.parent().find('label[_value='+theValue+']').size()==1)
		{
			worker.val('');
			worker.focus();
			worker.parent().parent().next().hide();
			return;
		}
		var label=$('<label _value="'+theValue+'">'+theValue+'<span class="glyphicon glyphicon-ok"></span><input type="hidden" name="{$param.name}[]" value="'+theValue+'" /></label>');
		label.find('span').click(function()
		{
			if($(this).hasClass('glyphicon-ok'))
			{
				$(this).removeClass('glyphicon-ok').addClass('glyphicon-remove');
				var val = $(this).parent().attr('_value');
				$(this).parent().find('input').val('!!!'+val);
			}else{
				$(this).addClass('glyphicon-ok').removeClass('glyphicon-remove');
				var val = $(this).parent().attr('_value');
				$(this).parent().find('input').val(val);
			}
		});
		label.dblclick(function()
		{
			label.animate({width:0,height:0},function(){
				$(this).remove();
			});
		});
		label.insertBefore(worker);
		worker.parent().parent().next().hide();

		worker.val('');
		worker.focus();
	}
	
	function robot()
	{
		if(worker.val()==prev_value) return;
		prev_value = worker.val();
		if(prev_value=='')
		{
			worker.parent().parent().next().hide();
			return;
		}
		var url = '__PHP__?class={input:text_select}';
		url += '&key_source=' + worker.attr('key_source');
		url += '&from_table=' + worker.attr('from_table');
		url += '&value_column=' + worker.attr('value_column');
		url += '&state=' + worker.attr('state');
		url += '&key='+prev_value;
		$.get(url,function(data){
			if(data.match(/<ul>\s+?<\/ul>/ig)){
				worker.parent().parent().next().hide();
				return;
			}
			worker.parent().parent().next().show();
			//alert(worker.position().left);
			worker.parent().parent().next().find('>div').html(data).css('margin-left',parseInt(worker.position().left)+parseInt(worker.parent().position().left));
			worker.parent().parent().next().find('>div').find('li').mouseover(function()
			{
				$(this).addClass('hover');
			}).mouseout(function(){
				$(this).removeClass('hover');
			}).click(function()
			{
				var theValue = $(this).attr('_value');

				if(worker.parent().find('label[_value='+theValue+']').size()==1)
				{
					worker.val('');
					worker.focus();
					worker.parent().parent().next().hide();
					return;
				}
				worker.val(theValue);
				generate_key();
				/*
				var label=$('<label _value="'+$(this).attr('_value')+'">'+$(this).attr('show')+'<span class="glyphicon glyphicon-remove"></span></label>');
				label.find('span').click(function()
				{
					var value_array = worker.prev().val().split(',');
					var value = '';
					for(var j in value_array)
					{
						if(value_array[j]!=theValue)
						{
							if(value!='') value+=',';
							value+=value_array[j];
						}
					}
					worker.prev().val(value);
					label.remove();
				});
				label.insertBefore(worker.prev());*/
				worker.parent().parent().next().hide();
				//worker.val($(this).attr('show'));
				var value_array;
				if(worker.prev().val()==''){
					value_array = new Array();
				}else{
					value_array = worker.prev().val().split(',');
				}
				value_array.push(theValue);
				
				worker.prev().val(value_array.join(','));
				worker.val('');
				worker.focus();
			});
		});
	}
});
//--></script>