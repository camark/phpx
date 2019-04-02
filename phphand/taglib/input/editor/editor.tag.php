<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else if(@$param.default_value){
	$value = $param.default_value;
}else{
	$value='';
}
?>
{if !isset($umeditor)}<?php $umeditor=true;?>
<link href="https://{$_SERVER.HTTP_HOST}__TAG__/um/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<script src="https://{$_SERVER.HTTP_HOST}__TAG__/um/umeditor.config.js"></script>
<script src="https://{$_SERVER.HTTP_HOST}__TAG__/um/umeditor.min.js"></script>
<script src="https://{$_SERVER.HTTP_HOST}__TAG__/um/lang/zh-cn/zh-cn.js"></script>
{/if}
<script type="text/plain" id="{$param.name}" name="{$param.name}" style="width:<?php if($param.width) echo $param.width .'px';else echo '600px';?>;height:240px;">{$value}</script>
<script type="text/javascript">var um = UM.getEditor('{$param.name}');</script>