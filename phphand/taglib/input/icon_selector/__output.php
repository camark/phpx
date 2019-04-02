<?php
class Icon_selectorOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		echo '<span class="glyphicon glyphicon-'.$data.'"></span>';
	}
}