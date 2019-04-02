<?php if(!isset($sql)) $sql="SELECT * FROM ". $table;?>
<html:form method="post" ajax="true">
	<output:table config="$table" data_source="$sql">{if isset($append_html)}{$append_html}{/if}</output:table>
</html:form>

<script type="text/javascript"><!--
$(function(){
	
});
//--></script>