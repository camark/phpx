<?php
if(!isset($chain_selector)){
$chain_selector=0;
?>
<div class="modal fade chain-selector-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="mmm">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">联级数据选择器</h4>
	  </div>
	  <div class="modal-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-4 the1">
						<div class="box category"><h5>分类查找</h5>
							<div class="bd">
							</div>
						</div>
					</div>
					<div class="col-md-4 the2">
						<div class="box target"><h5>请选择</h5>
							<div class="bd">
							</div>
						</div>
					</div>
					<div class="col-md-4 the3">
						<div class="box result"><h5>已选择</h5>
							<div class="bd">
								<ul></ul>
							</div>
						</div>
					</div>
				</div>
			</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
	  </div>
	</div>
  </div>
</div>
<link rel="stylesheet" type="text/css" href="__TAG__/tag.css" />
<script type="text/javascript"><!--
$(function(){
	$('input.chain_selector_printer').click(function(){
		var panel_id=$(this).attr('id')+'panel';
		if($('#'+panel_id).size()==0){
			var modal = $('.chain-selector-modal').clone();
			modal.attr('id',panel_id);
			modal.appendTo($('body'));
			modal.find('.modal-title').html($(this).attr('showname'));
			/*$('<div id="'+panel_id+'" selector="'+$(this).attr('id')+'"><div class="tree-selector-panel"></div></div>').appendTo($('body')).hide().modal({});*/
			modal.find('.modal-body').attr('selector',$(this).attr('id'));
			get_branch($(this),0,modal.find('.modal-body .box .bd:eq(0)'));
		}
		$('#'+panel_id).modal('show');
	});
	function branch_click(){
		var li=$(this).parent();
		if(li.find('ul').size()==0){
			var parent=li.parent();
			while(typeof(parent.attr('selector'))=='undefined') parent=parent.parent();
			var panel=parent;
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
	function target_click()
	{
		var label_html = $(this).find('label').html();
		var label_value = parseInt($(this).attr('vl'));
		
		var li = $(this);
		
		var parent=li.parent();
		while(typeof(parent.attr('selector'))=='undefined') parent=parent.parent();
		var panel=parent;
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
			
			var label=$('<li vl="'+label_value+'"><label>'+label_html+'</label><span class="glyphicon glyphicon-remove"></span></li>');
			label.appendTo(panel.find('.result ul'));
			label.find('span').click(remove_label);
						
			li.find('input').prop('checked',true);
			li.addClass('chosed');
		}else{
			panel.find('.result li[vl='+label_value+'] span').click();
		}
	}
	function remove_label()
	{
		var label = $(this).parent();
		var label_value = parseInt(label.attr('vl'));
		
		var parent=label.parent();
		while(typeof(parent.attr('selector'))=='undefined') parent=parent.parent();
		var panel=parent;
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

		panel.find('.target li[vl='+label_value+']').removeClass('chosed');
		panel.find('.target li[vl='+label_value+'] input').attr('checked',false);
	}
	function get_branch(selector,fid,append_target){
		var category_table=selector.attr('category_table');
		var category_value_column=selector.attr('category_value_column');
		var category_show_column=selector.attr('category_show_column');
		var category_fid_column=selector.attr('category_fid_column');
		$.get('?class={input:chain_selector}&category_table='+category_table+'&category_show_column='+category_show_column+'&category_value_column='+category_value_column+'&category_fid_column='+category_fid_column+'&fid='+fid,'',function(data,textStatus){
			if(textStatus=='success'){
				var ul=$(data);
				if(append_target.is('div')){
					ul.attr('class','level0');
				}else{
					append_target.find('span:eq(0)').removeClass('glyphicon-plus').addClass('glyphicon-minus');
					var level=parseInt(append_target.parent().attr('class').substring(5))+1;
					ul.attr('class','level'+level);
				}
				ul.find('span').click(branch_click);
				ul.find('label').click(get_target).mouseenter(function(){
					$(this).addClass('on');
				}).mouseleave(function(){
					$(this).removeClass('on');
				});
				ul.appendTo(append_target);
			}
		});
	}
	
	function get_target()
	{
		var label_value = parseInt($(this).parent().attr('vl'));
		
		var li = $(this).parent();
		var parent=li.parent();
		while(typeof(parent.attr('selector'))=='undefined') parent=parent.parent();
		var panel=parent;
		var selector=$('#'+panel.attr('selector'));
		
		panel.find('.category li.cur').removeClass('cur');
		li.addClass('cur');
	
		var target_table=selector.attr('target_table');
		var target_value_column=selector.attr('target_value_column');
		var target_show_column=selector.attr('target_show_column');
		var target_fid_column=selector.attr('target_fid_column');
		
		var category_table=selector.attr('category_table');
		var category_value_column=selector.attr('category_value_column');
		var category_fid_column=selector.attr('category_fid_column');
		
		var url = '?class={input:chain_selector}&method=target&target_table='+target_table+'&target_value_column='+target_value_column+'&target_show_column='+target_show_column+'&target_fid_column='+target_fid_column+'&fid='+label_value;
		
		url += '&category_table='+category_table;
		url += '&category_value_column='+category_value_column;
		url += '&category_fid_column='+category_fid_column;
		
		$.get(url,function(data,textStatus){
			if(textStatus=='success')
			{
				var ul=$(data);
				
				var theBox = panel.find('.target .bd');
				theBox.html('');
				
				ul.appendTo(theBox);
				
				ul.find('li').click(target_click);
			}
		});
	}
});
//--></script>
<?php }else{ $chain_selector++;}?>
<input type="hidden" name="{$param.name}" value="{$param.default_value}" />
<input
	type="text"
	pointer="{$param.name}"
	class="chain_selector_printer form-control"
	category_table="{$param.category_table}"
	category_value_column="{$param.category_value_column}"
	category_show_column="{$param.category_show_column}"
	category_fid_column="{$param.category_fid_column}"
	
	target_table="{$param.target_table}"
	target_value_column="{$param.target_value_column}"
	target_show_column="{$param.target_show_column}"
	target_fid_column="{$param.target_fid_column}"

	id="chain_selector{$tree_selector}"
	showname="{$param.showname}"
/>
