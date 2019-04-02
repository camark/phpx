<link rel="stylesheet" type="text/css" href="__TAG__/tag.css" />
{if !isset($two_selector)}<?php $two_selector=0;?>
<div class="modal fade two-selector-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">输入接口设置</h4>
	  </div>
	  <div class="modal-body">
	  	<div class="first-level"></div>
		<div class="second-level"></div>
		<div class="result"></div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
	  </div>
	</div>
  </div>
</div>
<script type="text/javascript">
$(function(){
	$('input.two_selector_printer').click(function(){
		var panel_id=$(this).attr('id')+'panel';
		if($('#'+panel_id).size()==0){
			var modal = $('.two-selector-modal').clone();
			modal.attr('id',panel_id);
			modal.appendTo($('body'));
			modal.find('.modal-title').html($(this).attr('showname'));
			/*$('<div id="'+panel_id+'" selector="'+$(this).attr('id')+'"><div class="two-selector-panel"></div></div>').appendTo($('body')).hide().modal({});*/
			modal.find('.modal-body').attr('selector',$(this).attr('id'));
			get_branch($(this),0,modal.find('.modal-body .first-level'));
		}
		$('#'+panel_id).modal('show');
	});
	function branch_click(){
		var li=$(this).parent();
		if(li.find('ul').size()==0){
			var parent=li.parent();
			while(!parent.is('div')) parent=parent.parent();
			var panel=parent.parent();
			var selector=$('#'+panel.attr('selector'));
			get_branch(selector,li.attr('vl'),li);
			return false;
		}
		if(li.find('ul:eq(0)').css('display')=='none'){
			li.find('ul:eq(0)').show();
			li.find('span:eq(0)').removeClass('glyphicon-plus').addClass('glyphicon-minus');
		}else{
			li.find('ul:eq(0)').hide();
			li.find('span:eq(0)').addClass('glyphicon-plus').removeClass('glyphicon-minus');
		}
	}
	function level2_click()
	{
		var label_html = $(this).html();
		var label_value = parseInt($(this).parent().attr('vl'));
		
		var li = $(this).parent();
		var parent=li.parent();
		while(!parent.hasClass('second-level')) parent=parent.parent();
		var panel=parent.parent();
		var selector=$('#'+panel.attr('selector'));
		
		var value_container = selector.prev();
		var values = value_container.val().split(',');
		var exists = false;
		for(var i in values)
		{
			var value = parseInt(values[i]);
			if(value==label_value)
			{
				exists = true;
				break;
			}
		}
		
		if(!exists)
		{
			if(value_container.val().trim()=='')
			{
				value_container.val(label_value);
				selector.val(label_html);
			}else{
				value_container.val(value_container.val()+','+label_value);
				selector.val(selector.val()+','+label_html);
			}
			
			var label=$('<label vl="'+label_value+'"><span class="glyphicon glyphicon-remove"></span>'+label_html+'</label>');
			label.appendTo(panel.find('.result'));
			label.find('span').click(remove_label);
		}
	}
	function remove_label()
	{
		var label = $(this).parent();
		var label_value = parseInt(label.attr('vl'));
		
		var panel=label.parent().parent();
		var selector=$('#'+panel.attr('selector'));
		
		var value_container = selector.prev();
		var values = value_container.val().split(',');
		var htmls = selector.val().split(',');
		
		var new_value = '';
		var new_html = '';
		for(var i in values)
		{
			var value = parseInt(values[i]);
			var html = htmls[i];
			if(value!=label_value)
			{
				if(new_value!='') new_value+=',';
				new_value += value;
				
				if(new_html!='') new_html+=',';
				new_html+=html;
			}
		}
		
		selector.val(new_html);
		value_container.val(new_value);
		label.remove();
	}
	function level1_click()
	{
		var li=$(this).parent();
		var fid = li.attr('vl');
		var selector = $('#'+li.parent().parent().parent().attr('selector'));
		get_branch(selector,fid,li.parent().parent().next());
	}
	
	function get_branch(selector,fid,append_target){
		var from_table=selector.attr('from_table');
		var value_column=selector.attr('value_column');
		var show_column=selector.attr('show_column');
		var fid_column=selector.attr('fid_column');
		$.get('?class={input:two_level_selector}&from_table='+from_table+'&show_column='+show_column+'&value_column='+value_column+'&fid_column='+fid_column+'&fid='+fid,'',function(data,textStatus){
			if(textStatus=='success'){
				var ul=$(data);
				if(append_target.hasClass('first-level')){
					ul.attr('class','level0');
					ul.find('span').click(level1_click);
				}else{
					append_target.html('');//.find('span:eq(0)').removeClass('glyphicon-plus').addClass('glyphicon-minus');
					var level=parseInt(append_target.parent().attr('class').substring(5))+1;
					ul.attr('class','level'+level);
					ul.find('span').click(level2_click);
				}
				ul.find('li').mouseenter(function()
				{
					$(this).addClass('hover');
				}).mouseleave(function()
				{
					$(this).removeClass('hover');
				});
				/*
				ul.find('span').click(branch_click);
				ul.find('label').click(select_label).mouseenter(function(){
					$(this).addClass('on');
				}).mouseleave(function(){
					$(this).removeClass('on');
				});*/
				ul.appendTo(append_target);
			}
		});
	}
});
</script>
{else}
<?php $two_selector++;?>
{/if}
<input type="hidden" name="{$param.name}" value="{$param.default_value}" />
<input
	type="text"
	pointer="{$param.name}"
	class="two_selector_printer form-control"
	from_table="{$param.from_table}"
	value_column="{$param.value_column}"
	show_column="{$param.show_column}"
	fid_column="{$param.fid_column}"
	id="two_selector{$two_selector}"
	showname="{$param.showname}"
/>
<span class="glyphicon glyphicon-plus form-control-feedback" aria-hidden="true" style="margin-right:17px;"></span>