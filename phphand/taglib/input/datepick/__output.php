<?php
class DatepickOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		if($data==0) echo '';
		else echo @date('y/m/d',$data);
	}
}