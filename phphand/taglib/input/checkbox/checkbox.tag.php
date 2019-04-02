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
$data_source = $param.data_source;
if(strpos($data_source,':')!==false)
{
	$data_source = $this->input->get_true_value($data_source);
}
if(is_array($data_source))
{
	$sh_options = $data_source;
}else{
	$sh_config = array('data_source'=> $data_source,'id_column'=>$param.id_column,'title_column'=>$param.title_column);$sh_options=$this->hp->get_options($sh_config);
	unset($sh_options['']);
	
}
?>
{if !isset($checkbox_style)}<?php $checkbox_style=true;?>
<style type="text/css">
.mr15{margin-right:15px;}
.mt5{margin-top:5px;display:inline-block;}
.mr15 input{vertical-align:-3px}
</style>
{/if}
{loop $sh_options as $sh_value => $sh_title}<span class="mt5 mr15"><input type="checkbox" name="{$param.name}{if $param.mode=='2'}_{$sh_value}{else}[]{/if}" value="{if $param.mode=='2'}1{else}{$sh_value}{/if}"{if in_array(''.$sh_value,$value)} checked="checked"{/if} />&nbsp;{$sh_title}</span>{/loop}