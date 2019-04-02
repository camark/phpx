<?php
class Max_datepickOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		echo @date('y/m/d H:i',$data);
	}
}