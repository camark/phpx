<?php
class CreatedateOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		if($data && is_int($data))
			echo '<span value="'.$data.'" days="'. floor((time()-$data)/24/3600).'">'.date('y/m/d H:i',$data).'</span>';
	}
}