<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
	if($value && preg_match('/^[0-9]+?$/is',$value)){
		if($param.method=='date')
			$value = date('Y-m-d',$value);
		else
			$value = date('Y-m-d H:i',$value);
	}
}else $value='';

if(!$value && @$param.default_value)
{
	$value = $param.default_value;
}

if(!$value && @$param.show_default){
	if($param.method=='date')
		$value = date('Y-m-d');
	else
		$value = date('Y-m-d H:i');
}

$datepick_name = 'datepick' . time() . rand(1000,9999);
if(!isset($datepick_flag)){
$datepick_flag=true;
?>
{if $this->client->is_mobile()}
<link rel="stylesheet" type="text/css" href="__TAG__/mobiscroll.custom-2.14.4.min.css" />
<script type="text/javascript" src="https://{$_SERVER['HTTP_HOST']}__TAG__/mobiscroll-2.14.4-crack.js"></script>
<script type="text/javascript"><!--
$(function()
{
	{if $param.method=='date'}
	$('input.datepicker').mobiscroll().date({
		theme: 'mobiscroll',
		display: 'bottom',
		dateFormat: 'yy-mm-dd',
		dateOrder: 'yymmdd',
	});
	{else}
	$('input.datepicker').mobiscroll().datetime({
		theme: 'mobiscroll',
		display: 'bottom',
		dateFormat: 'yy-mm-dd',
		dateOrder: 'yymmdd',
		timeFormat: 'HH:ii',
	});
	{/if}
	$('div.datepicker div').click(function(){
		$(this).prev().mobiscroll('show');
	});
});
//--></script>
{else}
<link rel="stylesheet" type="text/css" href="__TAG__/tag.css?" />
<script type="text/javascript" src="__TAG__/bootstrap.datepicker.js?"></script>
<script type="text/javascript"><!--
$(function()
{
	var page=__page__;
	page.find('input.datepicker').datepicker({
	    format: "yyyy-mm-dd",
	    language: "zh-CN",
	    autoclose: true,
	    todayHighlight: true
	});
	page.find('.date-remove').click(function()
	{
		$(this).next().click();
	});
});
//--></script>{/if}<?php }?>
<div class="input-group">
	<div class="input-group-addon date-remove"><span class="glyphicon glyphicon-calendar" style="font-size:10px;color:#999;"></span></div>
	<input type="text" class="form-control datepicker" id="{$datepick_name}" name="{$param.name}" value="{$value}" />
</div>
{if isset($ajax_update)}
<script type="text/javascript"><!--
$(function(){
	$('#{$datepick_name}').parent().css('max-width',150);
	$('#{$datepick_name}').change(function()
	{
		update_column($(this).val());
	});
});
//--></script>
{/if}
{if $param.tip && isset($INPUT_INTERFACE)}
<div class="input-tip">{$param.tip}</div>
{/if}