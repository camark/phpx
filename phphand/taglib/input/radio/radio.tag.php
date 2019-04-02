<?php
$value='';
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else if(@$param.default_value){
	$value = $param.default_value;
}

$data_source = $param.data_source;
if(strpos($data_source,':')!==false)
{
	$data_source = $this->input->get_true_value($data_source);
}
$sh_config = array('data_source'=> $data_source,'id_column'=>$param.id_column,'title_column'=>$param.title_column);$sh_options=$this->hp->get_options($sh_config);
unset($sh_options['']);
?>
{if !isset($checkbox_style)}<?php $checkbox_style=true;?>
<style type="text/css">
.mr15{margin-right:15px;}
.mt5{margin-top:5px;display:inline-block;}
.mr15 input{vertical-align:-3px;}
</style>
{/if}
{loop $sh_options as $sh_value => $sh_title}<span class="mt5 mr15"><input type="radio" name="{$param.name}" value="{$sh_value}"{if $value!=='' && $value!==false && $value==$sh_value} checked="checked"{/if} />&nbsp;{$sh_title}</span>{/loop}

{if isset($ajax_update)}
<script type="text/javascript"><!--
$('input[name={$param.name}]').click(function()
{
	update_column($(this).val());
});
//--></script>
{/if}