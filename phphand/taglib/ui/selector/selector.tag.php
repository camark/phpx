<define:name type="*" />
<define:default_value type="*" default="0" />
<define:from_table type="*" />
<define:show_column type="*" />
<define:value_column type="*" />
<define:state1 type="*" required="false" default="" />
<define:state2 type="*" required="false" default="" />
<?php
if(!isset($interface_selector)){
	$interface_selector=0;
}else{
	$interface_selector++;
}
$interface_selector_key=md5(md5($this->env->get('app_dir')).$this->config->get('security_key'));
$interface_selector_config_data_path=PHPHAND_DIR.'/taglib/ui/selector/data/'.$interface_selector_key . '.php';
if(!file_exists($interface_selector_config_data_path)){
	file_put_contents($interface_selector_config_data_path,"<?php\r\n\$path='".$this->env->get('app_dir')."';");
}
?>
<jquery:basic />
{if $interface_selector==0}
<script type="text/javascript"><!--
var interface_selector_loading='';
$(function(){
	$('.interface_selector').each(function(i){
		var from_table=$(this).attr('from_table');
		var value_column=$(this).attr('value_column');
		var show_column=$(this).attr('show_column');
		var key=$(this).attr('key');
		var id=$(this).attr('id');
		var default_value=$(this).find('input').val();
		var url='__TAG__/index.php?method=get_label';
		url+='&from_table='+from_table;
		url+='&value_column='+value_column;
		url+='&show_column='+show_column;
		url+='&value='+default_value;
		url+='&key='+key;
		url+='&id='+id;
		$.get(url,'',function(data,textStatus){
			if(textStatus=='success'){
				var id=data.documentElement.getAttribute('id');
				var label=data.documentElement.childNodes[0].data;
				$('#'+id+' .interface_selector_label').html(label);
				var name=$('#'+id).find('input').attr('name');
				if(eval("typeof("+name+'_change'+")")=="function"){
					eval(name+'_change()');
				}
			}
		});
	});
	
	$('.interface_selector_button,.interface_selector_label').click(function(){
		if($(this).parent().find('.interface_selector_box').css('display')=='none'){
			$(this).parent().find('.interface_selector_box_content').html('<div class="interface_selector_loading">loading..</div>');
			$(this).parent().find('.interface_selector_box').show();
			interface_selector_get_list($(this).parent().attr('id'),1);
		}else{
			if(interface_selector_loading==''){
				$(this).parent().find('.interface_selector_box').hide();
			}
		}
	});
});
function interface_selector_get_list(selector_id,page){
	var selector      = $('#'+selector_id);
	var from_table    = selector.attr('from_table');
	var value_column  = selector.attr('value_column');
	var show_column   = selector.attr('show_column');
	var key           = selector.attr('key');
	var id            = selector.attr('id');
	var state1=encodeURIComponent(selector.attr('state1'));
	var state2=encodeURIComponent(selector.attr('state2'));
	var default_value = selector.find('input').val();
	var url           = '__TAG__/index.php?method=get_list';
	url              += '&from_table='+from_table;
	url              += '&value_column='+value_column;
	url              += '&show_column='+show_column;
	url              += '&value='+default_value;
	url              += '&key='+key;
	url              += '&id='+id;
	url              += '&page='+page;
	url              += '&state1='+state1;
	url              += '&state2='+state2;

	interface_selector_loading=id;
	$.get(url,'',function(data,textStatus){
		if(textStatus=='success'){
			$('#'+interface_selector_loading+' .interface_selector_box_content').html(data);
			$('#'+interface_selector_loading+' .interface_selector_box_content li,#'+interface_selector_loading+' .interface_selector_box_content span').click(function(){
				var selector=$(this).parent().parent().parent().parent().parent().parent();
				selector.find('.interface_selector_label').html($(this).attr('title'));
				selector.find('.interface_selector_label').attr('title',$(this).attr('title'));
				selector.find('input').val($(this).attr('value'));
				selector.find('.interface_selector_box').hide();
				var name=selector.find('input').attr('name');
				if(eval("typeof("+name+'_change'+")")=="function"){
					eval(name+'_change()');
				}
			}).mouseover(function(){
				$(this).addClass('cursor');
			}).mouseout(function(){
				$(this).removeClass('cursor');
			});
			
			$('#'+interface_selector_loading+' .interface_selector_pagebar a').each(function(i){
				var selector=$(this).parent().parent().parent().parent().parent();
				$(this).attr('href','javascript:interface_selector_get_list("'+selector.attr('id')+'",'+$(this).attr('href')+')');
			});
			interface_selector_loading='';
		}
	});
}
//--></script>
{/if}
<link rel="stylesheet" type="text/css" href="__TAG__/style.css" />
<div class="interface_selector" id="interface_selector_{$interface_selector}" from_table="{$param.from_table}" value_column="{$param.value_column}" show_column="{$param.show_column}" key="{$interface_selector_key}" state1="{$param.state1}" state2="{$param.state2}">
	<span class="interface_selector_label"></span>
	<input type="hidden" name="{$param.name}" value="{$param.default_value}" />
	<span class="interface_selector_button"></span>
	<div class="interface_selector_box"><div class="interface_selector_box_content"></div></div>
	<div class="interface_selector_clear"></div>
</div>
