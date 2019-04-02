<?php $action = $_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING'];?>
<script>
function form_input_interface_part_callback(data)
{
	alert('tt');
}
</script>
<html:form method="post" ajax="true" action="$action" class="form-horizontal" callback="form_input_interface_part_callback">
	<form:input_part_interface config="$table" data="$row" part_index="$part_index"/>
</html:form>