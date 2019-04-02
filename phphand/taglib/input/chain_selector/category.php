<ul>
	<phphand:list sql="$sql" handle="$rs">
	<?php
	$count_child = $this->{"".$from_table}->count($fid_column . "=" . $rs[$value_column]);
	?>
	<li vl="{$rs[$value_column]}">
		<span class="{if $count_child>0}glyphicon glyphicon-chevron-right{else}{/if}"></span>
		<label>{$rs[$show_column]}</label>
		<div class="clear"></div>
	</li>
	</phphand:list>
</ul>