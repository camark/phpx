<?php

if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';

if(!$value && @$param.default_value){
	$value = $param.default_value;
}
$select_flag=time().rand(1000,9999);
$data_source = $param.data_source;
if(strpos($data_source,':')!==false)
{
	$data_source = $this->input->get_true_value($data_source);
}
?>
<base:tagcss src="http://{$_SERVER.HTTP_HOST}__TAG__/tag.css?3" />
<div class="tag-input-select form-control"{if $param.show_length} style="width:{$param.show_length}px;"{/if}>
	<select id="select{$select_flag}" name="{$param.name}">
	<?php
	$input_select_config = array('data_source'=> $data_source,'id_column'=>$param.id_column,'title_column'=>$param.title_column);
	$sh_options=$this->hp->get_options($input_select_config);
	?>
	{loop $sh_options as $sh_value => $sh_title}<option value="{$sh_value}"{if $value!=='' && $value!==false && $value==$sh_value} selected="selected"{/if}>{$sh_title}</option>{/loop}
	</select>
</div>
{if isset($ajax_update)}
<script type="text/javascript"><!--
$(function(){
	$('#select{$select_flag}').focus().width(110);
	$('#select{$select_flag}').blur(function()
	{
		update_column($(this).val());
	});
});
//--></script>
{/if}