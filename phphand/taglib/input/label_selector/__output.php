<?php
class SelectOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		$sh_options=$this->hp->get_options($config);
		if(isset($sh_options[$data])) echo '<span class="option_'.$data.'">'.$sh_options[$data].'</span>';
		else echo '&nbsp;';
	}
}