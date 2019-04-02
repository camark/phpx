<?php $action = $_SERVER['REQUEST_URI'];
if($this->query->get('refer'))
{
	$ref = base64_decode($this->query->get('refer'));
}else{
	$ref = $_SERVER['HTTP_REFERER'];
}
?>
<html:form method="post" ajax="true" action="{$action}" enctype="multipart/form-data" role="form" class="form-horizontal">
	<input type="hidden" name="phphand_auto_refer" value="{$ref}" />
	<?php if(isset($append_html)) echo $append_html;if(!isset($data)) $data=array();?>
	<form:input_interface config="$table" data="$data" />
</html:form>