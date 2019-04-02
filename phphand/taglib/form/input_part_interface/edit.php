<?php
$action = $_SERVER['REQUEST_URI'];
if(!isset($callback)) $callback='null';
?>
<script>
function form_input_interface_part_callback(data)
{
	$.fn.ajaxpostCallBack(data,{$callback});
}
</script>
<?php $jqueryBasic=true;$jqueryAjaxPost=true;?>
<html:form method="post" ajax="true" action="$action" class="form-horizontal" callback="form_input_interface_part_callback">
	<form:input_part_interface config="$table" data="$row" part_index="$part_index" part_sub_index="$part_sub_index"/>
</html:form>