<?php 
//part_index 在url地址中要+1
$action = $_SERVER['REQUEST_URI'];
?>
<html:form method="post" ajax="true" action="$action" class="form-horizontal">
	<form:input_interface config="$table" data="$row" part_index="$part_index" part_sub_index="$part_sub_index" />
</html:form>