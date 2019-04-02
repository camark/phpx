<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';

if(!$value && @$param.default_value){
	$value = $param.default_value;
}
$input_flag=time().rand(1000,9999);
?>
<input id="input{$input_flag}" type="text" class="form-control" name="{$param.name}" value="{$value}"{if  $param.show_length!=NULL} style="width:{$param.show_length}px;"{/if} placeholder="<?php if($param.tip) echo $param.tip;?>" />
{if isset($ajax_update)}
<script type="text/javascript"><!--
$(function(){
	$('#input{$input_flag}').focus().css('max-width',120);
	$('#input{$input_flag}').blur(function()
	{
		update_column($(this).val());
	});
});
//--></script>
{/if}
{if !defined('FORM_SEARCH') && $param.tip}
<div class="input-tip">{$param.tip}</div>
{/if}