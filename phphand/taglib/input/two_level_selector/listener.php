<ul>
	<phphand:list sql="$sql" handle="$rs">
	<?php
	$count_child = $this->{"".$from_table}->count($fid_column . "=" . $rs[$value_column]);
	?>
	<li vl="{$rs[$value_column]}" title="{$rs[$show_column]}"><span><?php if(mb_strlen($rs[$show_column],'utf-8')>15) echo mb_substr($rs[$show_column],0,14,'utf-8').'..';else echo $rs[$show_column];?></span></li>
	</phphand:list>
</ul>