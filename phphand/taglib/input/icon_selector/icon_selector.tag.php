<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';

if(!$value && @$param.default_value){
	$value = $param.default_value;
}
$icon_select_flag='icon_'. time() . rand(1000,9999);
?>
<script type="text/javascript"><!--
$(function()
{
	var page = __page__;
	var trigger = page.find('span#{$icon_select_flag}');
	var valuer = trigger.prev();
	var selection = trigger.parent().prev();
	selection.find('span').click(function()
	{
		$(this).parent().find('.selected').removeClass('selected');
		$(this).addClass('selected');
		valuer.val($(this).attr('value'));
		trigger.attr('class','glyphicon glyphicon-'+$(this).attr('value'));
		$(this).parent().parent().parent().parent().parent().modal('hide');
		{if isset($ajax_update)}update_column($(this).attr('value')){/if}
	}).mouseenter(function()
	{
		$(this).addClass('hover');
	}).mouseleave(function()
	{
		$(this).removeClass('hover');
	});
	trigger.click(function()
	{
		valuer.showDialog({
			title : '请选择',
			content : selection,
			submit : function()
			{
			}
		});
		selection.show();
	});
	
	{if isset($ajax_update)}trigger.click(){/if}
});
//--></script>
<style type="text/css"><!--
.icon_list span{width:20px;height:20px;text-align:center;line-height:20px;cursor:pointer;}
.icon_list span.hover{background:#ffa;}
.icon-selector span{width:20px;height:20px;top:7px;cursor:pointer;text-align:center;line-height:20px;}
--></style>
<div style="display:none;" selector_id="{$icon_select_flag}" class="icon_list">
	<?php $input_select_config = array('data_source'=> $param.data_source,'id_column'=>$param.id_column,'title_column'=>$param.title_column);$sh_options=$this->hp->get_options($input_select_config);?>
	{loop $sh_options as $sh_value => $sh_title}<span class="glyphicon glyphicon-{$sh_value}{if $sh_value==$value} selected{/if}" value="{$sh_value}"></span>{/if}
</div>
<div class="icon-selector">
	<input type="hidden" name="{$param.name}" value="{$value}" /><span id="{$icon_select_flag}" class="icon-selector glyphicon glyphicon-{$value}" style="border:1px solid #ccc;"></span>
</div>