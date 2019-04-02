{if isset($ajax_update)}FALSE{else}
<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else if($param.value!==''){
	$value = $this->input->get_true_value($param.value);
}else{
	$value='';
}

$input_flag=time().rand(1000,9999);
?>
<input type="hidden" name="{$param.name}" value="{$value}" />
{/if}