{if isset($ajax_update)}
<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';

if(!$value && @$param.default_value){
	$value = $param.default_value;
}
?>
<input type="text" class="form-control" name="{$param.name}" value="{$value}" />
<script type="text/javascript"><!--
$('input[name={$param.name}]').focus().css('max-width',120);
$('input[name={$param.name}]').blur(function()
{
	update_column($(this).val());
});
//--></script>
{/if}