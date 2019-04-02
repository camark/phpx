<?php
if(isset($row))
{
	$value = $this->input->get_value($row,$param.name);
}else $value='';
if(!$value) $value=0;
$value=(int)$value-8*3600;
$hour = date('H',$value);
$minute = date('i',$value);
?>
<select name="{$param.name}_hour" class="form-control" style="display:inline-block;width:83px;">
<?php for($h=0;$h<24;$h++){
	echo '<option value="' . $h .'"';
	if($h==$hour) echo ' selected';
	echo '>'. $h .'时</option>';
}
?></select>
: 
<select name="{$param.name}_minute" class="form-control" style="display:inline-block;width:83px;">
<?php for($h=0;$h<59;$h++){
	echo '<option value="' . $h .'"';
	if($h==$minute) echo ' selected';
	echo '>'. $h .'分</option>';
}
?></select>