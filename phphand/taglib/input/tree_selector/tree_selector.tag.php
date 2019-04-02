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
{if !isset($tree_selector)}
<?php $tree_selector=0;?>
<link rel="stylesheet" type="text/css" href="http://{$_SERVER.HTTP_HOST}__TAG__/tree_selector.css" />
{if $this->client->is_mobile() && false}
<base:drawer />
<div class="input_interface_drawer tree-selector-modal" style="display:none;"></div>
{else}
<div class="modal fade tree-selector-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">输入接口设置</h4>
	  </div>
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
	  </div>
	</div>
  </div>
</div>
{/if}
<script type="text/javascript">
if(typeof String.prototype.trim=='undefined'){
	String.prototype.trim = function () {
		return this .replace(/^\s\s*/, '' ).replace(/\s\s*$/, '' );
	}
}
$(function(){
	function init(panel_id)
	{
		if($('#'+panel_id).size()==0){
			var selector_id = panel_id.replace(/panel$/ig,'');
			var selector = $('#'+selector_id);
			var modal = $('.tree-selector-modal:eq(0)').clone().removeClass('tree-selector-modal').addClass('tree-selector');
			modal.attr('id',panel_id);
			modal.appendTo($('body'));
			modal.find('button.close').unbind().click(function()
			{
				modal.modal('hide');
			});
			
			if(modal.find('.modal-body').size()>0){
				modal.find('.modal-title').html(selector.attr('showname'));
				modal.find('.modal-body').attr('selector',selector_id);
				modal.find('.modal-body').html('<div class="selector"></div><div class="result"></div>');
			}else{
				modal.drawer({position:'right',showIcon:true,background:'white'});
				modal.find('.drawer-content').html('<div class="selector"></div><div class="result"></div>');
				modal.find('.drawer-content').attr('selector',selector_id);
				
				modal.find('.selector').css({
				'max-height':'none',
				'-webkit-overflow-scrolling' : 'touch',
				'-webkit-box-sizing' : 'border-box'
				}).height($(window).height()- 36 - 80);
				modal.show();
			}
	
			var value_container = selector.prev();
			var values;
			if(value_container.val().trim()=='')
			{
				values=new Array();
			}else{
				values  = value_container.val().split(',');
			}
			
			if(selector.val()=='')
			{
				return;
			}
			var htmls = selector.val().split(',');
			for(var i in values)
			{
				var label_html = htmls[i];
				var label_value = values[i];
				if(label_value=='') continue;
				var label=$('<label vl="'+label_value+'"><span class="glyphicon glyphicon-remove"></span>'+label_html+'</label>');
				label.appendTo(modal.find('.result'));
				label.find('span').click(remove_label);
			}
		}
	}
	var page = __page__;
	page.find('input.tree_selector_printer[inited=false]').click(function(){
		var panel_id=$(this).attr('id')+'panel';
		init(panel_id);
		var container;
		if($('#'+panel_id).find('.selector').html().trim()==''){
			get_branch($(this),0,$('#'+panel_id).find('.selector'));
		}
		if($('#'+panel_id).find('.modal-body').size()==1){
			$('#'+panel_id).modal('show');
		}else{
			$('#'+panel_id).drawer('show');
		}
		
		{if isset($ajax_update)}
		$('#'+panel_id).on('hide.bs.modal',function()
		{
			//update_column($(this).val());
			var id = panel_id.replace(/panel$/ig,'');
			var value= $('#'+id).prev().val();
			update_column(value);
		});
		{/if}
	}).each(function(i)
	{
		var value = $(this).prev().val();
		var selector = $(this);
		var from_table=selector.attr('from_table');
		var value_column=selector.attr('value_column');
		var show_column=selector.attr('show_column');
		var fid_column=selector.attr('fid_column');
		var state=selector.attr('state');
		
		$.get('http://{$_SERVER.HTTP_HOST}__PHP__?class={input:tree_selector}&method=get_default_show&value='+value+'&from_table='+from_table+'&show_column='+show_column+'&value_column='+value_column+'&fid_column='+fid_column+'&state='+encodeURIComponent(state),function(data,textStatus)
		{
			var panel_id=selector.attr('id')+'panel';
			selector.val(data);
			init(panel_id);
			
			{if isset($ajax_update)}
			selector.click();
			{/if}
		});
		{if isset($ajax_update)}selector.css('max-width',120);{/if}
		$(this).attr('inited','true');
	});
	function branch_click(){
		if(typeof $(this).attr('class')=='undefined' || $(this).attr('class')=='') return;
		var li=$(this).parent();
		if(li.find('p').size()==1) return;
		if(li.find('ul').size()==0){
			var parent=li.parent();
			while(!parent.is('div')) parent=parent.parent();
			var panel=parent.parent();
			var selector=$('#'+panel.attr('selector'));
			get_branch(selector,li.attr('data_id'),li);
			return false;
		}
		if(li.find('ul:eq(0)').css('display')=='none'){
			li.find('ul:eq(0)').show();
			li.find('span:eq(0)').removeClass('glyphicon-triangle-right').addClass('glyphicon-triangle-bottom');
		}else{
			li.find('ul:eq(0)').hide();
			li.find('span:eq(0)').addClass('glyphicon-triangle-right').removeClass('glyphicon-triangle-bottom');
		}
	}
	function select_label()
	{
		var label_html = $(this).find('label').html();
		var label_value = $(this).parent().attr('vl');
		
		if(label_value==''){
			$(this).parent().find('input:eq(0)').prop('checked',false);
			return;
		}
		
		
		var li = $(this).parent();
		var parent=li.parent();
		while(!parent.is('div')) parent=parent.parent();
		var panel=parent.parent();
		var selector=$('#'+panel.attr('selector'));
		if($(this).prev().hasClass('glyphicon') && selector.attr('final_only')=='1')
		{
			alert('只能选择无子数据的');
			return;
		}
		
		var value_container = selector.prev();
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
			if(selector.attr('max_selection') == 1)
			{
				panel.find('.result label span').each(function(i)
				{
					$(this).click();
				});
			}
			
			if(selector.attr('max_selection')>1 && values.length == selector.attr('max_selection'))
			{
				li.find('input:eq(0)').prop('checked',false);
				alert('最多只能选择'+selector.attr('max_selection')+'个选项');
				return;
			}
			if(values.length==0 || value_container.val()=='')
			{
				value_container.val(label_value);
				selector.val(label_html);
			}else{
				value_container.val(value_container.val()+','+label_value);
				selector.val(selector.val()+','+label_html);
			}
			
			value_container.change();
			
			var label=$('<label vl="'+label_value+'"><span class="glyphicon glyphicon-remove"></span>'+label_html+'</label>');
			label.appendTo(panel.find('.result'));
			label.find('span').click(remove_label).mouseover(function()
			{
				$(this).addClass('hover');
			}).mouseleave(function()
			{
				$(this).removeClass('hover');
			});
			$(this).find('input').prop('checked',true);
		}else{
			panel.find('.result label[vl='+label_value+'] span').click();
		}
	}
	function remove_label()
	{
		var label = $(this).parent();
		var label_value = label.attr('vl');

		
		var panel=label.parent().parent();
		
		
		panel.find('.selector li[vl='+label_value+'] input:eq(0)').prop('checked',false);
		if(panel.find('.selector li[vl='+label_value+'] li[vl='+label_value+'] input:eq(0)').size()>0)
			panel.find('.selector li[vl='+label_value+'] li[vl='+label_value+'] input:eq(0)').prop('checked',false);
		var selector=$('#'+panel.attr('selector'));
		
		var value_container = selector.prev();
		var values;
		if(value_container.val().trim()=='')
		{
			values=new Array();
		}else{
			values  = value_container.val().split(',');
		}
		var htmls = selector.val().split(',');
		
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
		
		selector.val(new_html);
		value_container.val(new_value);
		label.remove();
	}
	function get_branch(selector,fid,append_target){
		var from_table=selector.attr('from_table');
		var value_column=selector.attr('value_column');
		var show_column=selector.attr('show_column');
		var fid_column=selector.attr('fid_column');
		var state = encodeURIComponent(selector.attr('state'));
		var value = selector.prev().val();
		append_target.append('<p style="padding:0 0 0 39px;"><base:loading /></p>');
		var url = 'http://{$_SERVER.HTTP_HOST}__PHP__?class={input:tree_selector}&from_table='+from_table+'&show_column='+show_column+'&value_column='+value_column+'&fid_column='+fid_column+'&fid='+fid+'&state='+state+'&value='+value+'&multi_parent={$param.multi_parent}';
		if(location.href.indexOf('{$_SERVER.HTTP_HOST}')==-1 &&  (/msie/.test(navigator.userAgent.toLowerCase())))
		{
			url = location.href.replace(/^(http:\/\/[^\/]+?)\/.+?$/ig,"$1") + '/js/router.php?'+url;
		}
		$.get(url,function(data,textStatus){
			if(textStatus=='success'){
				var ul=$(data);
				append_target.find('p').remove();
				if(append_target.is('div')){
					ul.attr('class','level0');
				}else{
					append_target.find('span:eq(0)').removeClass('glyphicon-triangle-right').addClass('glyphicon-triangle-bottom');
					var level=parseInt(append_target.parent().attr('class').substring(5))+1;
					ul.attr('class','level'+level);
				}
				ul.find('span').click(branch_click);
				ul.find('div').click(select_label).mouseenter(function(){
					$(this).addClass('on');
				}).mouseleave(function(){
					$(this).removeClass('on');
				});
				ul.find('li div').mouseover(function()
				{
					$(this).addClass('on');
				}).mouseleave(function()
				{
					$(this).removeClass('on');
				});
				ul.appendTo(append_target);
			}
		});
	}
});
</script>
{else}
<?php $tree_selector++;?>
{/if}
<div class="input-group">
	<input type="hidden" name="{$param.name}" value="{$value}" />
	<input type="text"
		pointer="{$param.name}"
		class="tree_selector_printer form-control"
		from_table="{$param.from_table}"
		value_column="{$param.value_column}"
		show_column="{$param.show_column}"
		fid_column="{$param.fid_column}"
		state="<?php echo $this->view->get_var($this->input->get_true_value($param.state));?>"
		id="tree_selector{$tree_selector}{$ts_flag}"
		max_selection = "{$param.max_selection}"
		showname="{$param.showname}"
		inited="false"
		final_only="{$param.final_only}"
		readonly
		style="cursor:pointer;"
	/>
	<div class="tree-selector-addon input-group-addon"><span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span></div>
</div>