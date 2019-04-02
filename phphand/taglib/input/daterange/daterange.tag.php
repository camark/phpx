<?php
$dname1 = $param.name . '_begin';
$dname2= $param.name . '_end';
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name,'explode');
	if(is_array($value) && sizeof($value)==2)
	{
		$value[0] = (int)$value[0];
		$value[1] = (int)$value[1];
		
		$value1 = ($value[0]>0)?date('Y-m-d',$value[0]):'';
		$value2 = ($value[1]>0)?date('Y-m-d',$value[1]):'';
	}else{
		$value1 = '';
		$value2 = '';
	}
}else{
	$value1='';
	$value2='';
}
?>
<div style="display:block;max-width:350px;" class="date-range">
	<div style="display:inline-block;width:45%;float:left;">
		<input:datepick name="$dname1" default_value="$value1" />
	</div>
	<div style="display:inline-block;width:10%;height:30px;line-height:30px;float:left;text-align:center;">{~到}</div>
	<div style="display:inline-block;width:45%;float:left;">
		<input:datepick name="$dname2" default_value="$value2" />
	</div>
</div>