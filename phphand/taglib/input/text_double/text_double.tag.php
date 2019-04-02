<?php
if(isset($row))
{
	$get_value=$this->input->get_value($row,$param.name,'explode');
	if(is_array($get_value) && sizeof($get_value)>1){
		list($value1,$value2) = $get_value;
	}else if($param.name_position==2){
		$value1 = $this->input->get_value($row,$param.name . $param.name1);
		$value2 = $this->input->get_value($row,$param.name . $param.name2);
	}else if($param.name_position==1){
		$value1 = $this->input->get_value($param.name1 . $row,$param.name);
		$value2 = $this->input->get_value($param.name2 . $row,$param.name);
	}else{
		$value1='';
		$value2='';
	}
}else{
	$value1='';
	$value2='';
}

/*
$value2='';
if(isset($row))
{
	$value2 = $this->input->get_value($row,$param.name_position==1? $param.name2 . $param.name : $param.name . $param.name2);
}else{
	$value2 = '';
}*/
?>
<input type="text" name="{$param.name}{$param.name1}" size="5" value="{$value1}" class="form-control inline" style="width:90px;display:inline-block;" /> <sm>-</sm> <input type="text" name="{$param.name}{$param.name2}" value="{$value2}" size="5" class="form-control inline" style="width:90px;display:inline-block;" /> <sm>{$param.unit}</sm>