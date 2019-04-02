<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else if(@$param.default_value){
	$value = $param.default_value;
}else{
	$value = "";
}
$value = str_replace('</p>',"\n",$value);
$value = str_replace("<br/>","\n",$value);
$value = preg_replace('/<[^>]+?>/is','',$value);
$input_flag=time().rand(1000,9999);
?>
<textarea name="{$param.name}" class="form-control" cols="{$param.cols}" rows="{$param.rows}">{$value}</textarea>