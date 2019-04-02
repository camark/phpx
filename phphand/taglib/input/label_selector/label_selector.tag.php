<?php

if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';

if(!$value && @$param.default_value){
	$value = $param.default_value;
}
$array = explode(',',$value);
$value = array();
foreach($array as $val)
{
	$val = trim($val);
	if(!$val) continue;
	$value[]=''.$val;
}


$select_flag=time().rand(1000,9999);
$data_source = $param.data_source;
if(strpos($data_source,':')!==false)
{
	$data_source = $this->input->get_true_value($data_source);
}

$input_select_config = array('data_source'=> $data_source,'id_column'=>$param.id_column,'title_column'=>$param.title_column);
$sh_options=$this->hp->get_options($input_select_config);
unset($sh_options['']);
?>
<div class="label_selector" name="{$param.name}">
	{loop $sh_options as $sh_value => $sh_title}
		<label value="{$sh_value}"{if in_array(''.$sh_value,$value)} class="selected"{/if}>{$sh_title}</label>
		{if in_array(''.$sh_value,$value)}<input type="hidden" name="{$param.name}[]" value="{$sh_value}" />{/if}
	{/loop}
</div>
{if !isset($label_selector_init)}<?php $label_selector_init=1;?>
<link rel="stylesheet" type="text/css" href="__TAG__/tag.css" />
<script type="text/javascript"><!--
$(function()
{
	var page=__page__;
	page.find('.label_selector label').click(function()
	{
		if($(this).hasClass('selected'))
		{
			$(this).removeClass('selected');
			$(this).next().remove();
		}else{
			$(this).addClass('selected');
			var input = $('<input type="hidden" name="'+$(this).parent().attr('name')+'[]" value="'+$(this).attr('value')+'" />');
			input.insertAfter($(this));
		}
	});
});
//--></script>
{/if}
