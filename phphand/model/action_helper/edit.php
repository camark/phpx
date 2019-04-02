<?php $action = $_SERVER['REQUEST_URI'];?>
<html:form method="post" ajax="true" action="$action" class="form-horizontal">
	<input type="hidden" name="phphand_auto_refer" value="{$_SERVER['HTTP_REFERER']}" />
	<form:input_interface config="$table" data="$row" />
</html:form>