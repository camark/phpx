<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else if(@$param.default_value){
	$value = $param.default_value;
}else{
	$value = '';
}
?>
<input type="checkbox" style="margin-top:8px;" name="{$param.name}" value="{$param.check_value}"{if $value==$param.check_value} checked="checked"{/if} />