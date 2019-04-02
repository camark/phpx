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
$ts_flag=time() . rand(1000,9999);
?>
{if !isset($input_select)}
<?php $input_select=0;?><link rel="stylesheet" type="text/css" href="__TAG__/tag.css" />
{else}
<?php $input_select++;?>
{/if}
<div class="input-group form-control input-select-input-group" style="padding:0;">
	<div class="input-group-long" style="padding:6px 12px;">
		<input type="hidden" name="{$param.name}" value="{$value}" />
		<input type="text"
			pointer="{$param.name}"
			class="input_select_printer"
			from_table="{$param.from_table}"
			value_column="{$param.value_column}"
			show_column="{$param.show_column}"
			state="<?php echo $this->view->get_var($param.state);?>"
			id="input_select{$input_select}{$ts_flag}"
			max_selection = "{$param.max_selection}"
			showname="{$param.showname}"
			style="border:0;outline:none;"
			inited="false"
		/>
	</div>
</div>
<div class="input-select" style="display:none;">
	<div class="input-select-true">
	</div>
</div>

<?php if(!isset($input_select_script)){
$input_select_script=true;?>
<script type="text/javascript"><!--
$(function()
{
	var page=__page__;
	var interval = null;
	var worker = null;
	var prev_value = '%%%|||%%%';
	page.find('.input-select').each(function(i)
	{
		var value = $(this).prev().find('input:hidden').val();
		var selector = $(this).prev().find('input:text');
		var from_table=selector.attr('from_table');
		var value_column=selector.attr('value_column');
		var show_column=selector.attr('show_column');
		var state=selector.attr('state');

		var input_select = $(this);

		input_select.parent().parent().click(function(){
			input_select.focus();
		});

		$.get('https://{$_SERVER.HTTP_HOST}__PHP__?class={input:input_select}&method=get_default_show&value='+value+'&from_table='+from_table+'&show_column='+show_column+'&value_column='+value_column+'&state='+encodeURIComponent(state),function(data,textStatus)
		{
			var temp = $('<div>'+data+'</div>');
			temp.find('label').each(function(i){
				var label = $(this);
				label.find('span').click(function()
				{	
					var theValue=$(this).parent().attr('_value');
					var value_array = selector.prev().val().split(',');
					var value = '';
					for(var j in value_array)
					{
						if(value_array[j]!=theValue)
						{
							if(value!='') value+=',';
							value+=value_array[j];
						}
					}
					selector.prev().val(value);
					label.remove();
				});
				label.insertBefore(selector.prev());
			});
		});
		{if isset($ajax_update)}selector.css('max-width',120);{/if}

		//$(this).css('width',$(this).prev().css('width'));

		$(this).prev().find('input:text').focus(function()
		{
			worker = $(this);
			interval = setInterval(robot,100);
			prev_value = $(this).val()==''?'%%%|||%%%':$(this).val();
		}).blur(function()
		{
			clearInterval(interval);
			prev_value = '';
			setTimeout(function(){
				if(!selector.is('focus'))
					input_select.hide();
			},200);
		}).keydown(function(e)
		{
			if(worker.parent().parent().next().css('display')=='none'){
				if(e.which==8)
				{
					if($(this).val()=='' && $(this).parent().find('label').size()>0)
					{
						var label = $(this).parent().find('label:last');
						var value = label.attr('_value');
						var new_array = [];
						var values = $(this).prev().val().replace(/\s/ig,'');
						var array;
						if(values=='')
						{
							array=[];
						}else{
							array=values.split(',');
						}
						for(var i in array)
						{
							//console.log(array[i]+','+value);
							if(array[i]!=value)
							{
								new_array.push(array[i]);
							}
						}
						$(this).prev().val(new_array.join(','));

						$(this).parent().find('label:last').remove();
					}
				}
				return;
			}
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
				case 13:
					if(worker.parent().parent().next().find('li.hover').size()==1)
					{
						worker.parent().parent().next().find('li.hover').click();
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
				default:
					console.log(e.which);
			}
			return false;
		});
	});
	
	function robot()
	{
		if(worker.val()==prev_value) return;
		prev_value = worker.val();
		if(prev_value=='')
		{
			//worker.parent().parent().next().hide();
			//return;
		}
		var url = '__PHP__?class={input:input_select}';
		url += '&from_table=' + worker.attr('from_table');
		url += '&value_column=' + worker.attr('value_column');
		url += '&show_column=' + worker.attr('show_column');
		url += '&state=' + worker.attr('state');
		url += '&key='+prev_value;
		$.get(url,function(data){
			if(data.match(/<ul>\s+?<\/ul>/ig)){
				worker.parent().parent().next().hide();
				return;
			}
			worker.parent().parent().next().show();
			worker.parent().parent().next().find('>div').html(data);
			worker.parent().parent().next().find('>div').find('li').mouseover(function()
			{
				$(this).addClass('hover');
			}).mouseout(function(){
				$(this).removeClass('hover');
			}).click(function()
			{
				var theValue = $(this).attr('_value');
				var array;
				var values=worker.prev().val().replace(/\s/ig,'');
				if(values=='' || values=='0')
				{
					array=[];
				}else{
					array=values.split(',');
				}
				var exists=false;
				for(var i in array)
				{
					if(array[i]==theValue)
					{
						exists=true;
						break;
					}
				}
				if(exists)
				{
					return;
				}
				if(worker.parent().find('label[_value=\''+theValue+'\']').size()==1)
				{
					worker.val('');
					worker.focus();
					worker.parent().parent().next().hide();
					return;
				}
				var label=$('<label _value="'+$(this).attr('_value')+'">'+$(this).attr('show')+'<span class="glyphicon glyphicon-remove"></span></label>');
				label.find('span').click(function()
				{
					var value_array;
					if(worker.prev().val()=='' || worker.prev().val()=='0')
					{
						value_array=[];
					}else{
						value_array = worker.prev().val().split(',');
					}
					
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
				label.insertBefore(worker.prev());
				worker.parent().parent().next().hide();
				worker.val($(this).attr('show'));
				var value_array;
				if(worker.prev().val()=='' || worker.prev().val()=='0'){
					value_array = new Array();
				}else{
					value_array = worker.prev().val().split(',');
				}
				value_array.push(theValue);
				
				worker.prev().val(value_array.join(','));
				worker.val('');
				//worker.focus();
			});
		});
	}
});
//--></script>
<?php }?>