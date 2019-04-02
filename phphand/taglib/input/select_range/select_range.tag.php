<?php
if($param.name1==$param.name2)
{
	$name1 = $name2 = $param.name . '[]';
}else{
	$name1 = $param.name . $param.name1;
	$name2 = $param.name . $param.name2;
}
?>
<input:select name="$name1" data_source="{$param.data_source1}" show_length="100" /> - <input:select name="$name2" data_source="{$param.data_source2}" show_length="100" />
<script type="text/javascript"><!--
$("select[name='{$name1}']").change(function()
{
	$(this).next().find('option').attr('selected',false);
	$(this).next().find('option:last').attr('selected',true);
});
//--></script>