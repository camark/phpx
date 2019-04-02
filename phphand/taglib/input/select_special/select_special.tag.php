<?php
$value='';
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else if(@$param.default_value){
	$value = $this->input->get_true_value($param.default_value);
}
if(!isset($ssn)) $ssn=1;
else $ssn++;
$select_special_flag=$ssn . time().rand(1000,9999);
?>
<select name="{$param.name}" id="ss{$select_special_flag}" class="form-control">
	
</select>
<script type="text/javascript">
$(function(){
	var url = 'http://{$_SERVER.HTTP_HOST}__PHP__?class={input:select_special}';
	if(location.href.indexOf('{$_SERVER.HTTP_HOST}')==-1 &&  (/msie/.test(navigator.userAgent.toLowerCase())))
	{
		url = location.href.replace(/^(http:\/\/[^\/]+?)\/.+?$/ig,"$1") + '/js/router.php?'+url;
	}
	$.post(url,{
		'url' : '<?php echo str_replace("'","\\'",$param.url);?>',
		'item' : '<?php echo str_replace("'","\\'",$param.item);?>',
		'key' : '<?php echo str_replace("'","\\'",$param.key);?>',
		'value' : '<?php echo str_replace("'","\\'",$param.value);?>',
		'cookie_path' : '<?php if(strpos($param.cookie_path,'.')>0) echo $param.cookie_path;else echo  @str_replace("'","\\'",$this->_var[$param.cookie_path]);?>'
	},function(data)
	{
		if(data=='') data='<option>无选项</option>';
		$('#ss{$select_special_flag}').html(data);
	});
});
</script>