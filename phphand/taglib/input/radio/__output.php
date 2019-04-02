<?php
class RadioOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		$sh_options=$this->hp->get_options($config);
		if(isset($sh_options[$data])) echo $sh_options[$data];
		else echo '&nbsp;';
	}
}