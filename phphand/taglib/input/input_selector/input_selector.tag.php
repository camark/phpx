<?php
if(isset($row[$param.name]))
{
	$value = $this->input->get_value($row,$param.name);
	$input_config=unserialize($value);
}else{
	$input_config=array();
}

$all_inputs = $this->input->get_grouped_inputs();
if(!isset($input_selector_count)) $input_selector_count=0;
else $input_selector_count++;
$input_selector_flag = $input_selector_count . '_' . time() .'_' . rand(1000,9999);
?>
<style type="text/css"><!--
option.date{background:#00CCCC;color:white;}
option.text{background:#FF3333;color:white;}
option.select{background:#339933;color:white;}
option.file{background:#9999FF;color:white;}
option.helper{background:#999;color:white;}
option.example{background:#330066;color:white;}
--></style>
<textarea name="input-config" style="display:none;">{$value}</textarea>
<select flag="{$input_selector_flag}" name="input" class="form-control">
	<option value="">无输入方式</option>
	{loop $all_inputs as $group => $inputs}
	<option value="" disabled="disabled">
		<?php switch($group){
		case 'text' : echo '文本类';break;
		case 'date' : echo '日期类';break;
		case 'select' : echo '选择类';break;
		case 'file' : echo '上传类';break;
		case 'helper' : echo '辅助类';break;
		case 'example' : echo '自定义可复用';break;
		}?>
	</option>
		{loop $inputs as $key => $input}
		<option class="{$group}" value="{$input}"{if isset($input_config.input) && $input==$input_config.input} selected="selected"{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&gt;&nbsp;{$key}</option>
		{/loop}
	{/loop}
</select>
<div class="form-container" id="input_selector{$input_selector_flag}">
</div>
<script type="text/javascript"><!--
$(function(){
	var page = __page__;
	function specialize_input_config(input)
	{
		$.post('?class={input:input_selector}&method=get_input_special&input='+input,{'config':page.find('select[name=input][flag={$input_selector_flag}]').prev().val()},function(data)
		{
			page.find('#input_selector{$input_selector_flag}').html(data);
		});
	}
	specialize_input_config('{$input_config.input}');
	page.find('select[name=input][flag={$input_selector_flag}]').change(function()
	{
		specialize_input_config($(this).val());
	});
});
//--></script>