{if isset($ajax_update)}FALSE{else}
<?php
$value='';
if(isset($row[$param.name]))
{
	$value = $this->input->get_value($row,$param.name);
}else if(@$param.default_value){
	$value = $param.default_value;
}
$input_flag=time().rand(1000,9999);
?>
<input type="hidden" _name_="{$param.name}" value="{$value}" />
<div class="controller_container"></div>
{/if}