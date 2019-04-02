<?php
$input_table_column_num = intval($param.column_num);
$input_table_rows = explode(',',$param.rows);
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
	if($value)
		$value = unserialize($value);
	else
		$value = array();
}else{
	$value = array();
}
?>
<link rel="stylesheet" type="text/css" href="__TAG__/tag.css"></link>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="input_table">
	<thead>
		<th>&nbsp;</th>
		<?php for($i=1;$i<=$input_table_column_num;$i++){?>
		<th><?php echo str_replace('[n]',$i,$param.column_pattern);?></th>
		<?php }?>
	</thead>
	<tbody>
		{loop $input_table_rows as $input_i => $input_table_row_name}
		<tr>
			<td>{$input_table_row_name}</td>
			<?php for($i=1;$i<=$input_table_column_num;$i++){?>
			<td><input type="text" name="{$param.name}_{$input_i}${$i}" size="4" value="{$value[$input_i][$i]}" /></td>
			<?php }?>
		</tr>
		{/loop}
	</tbody>
</table>