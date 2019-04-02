<?php
class Server extends PHPHand_Action
{
	function get_input_special()
	{
		$input = $this -> query->get('input');
		$field_config = unserialize($_POST['config']);
		
		if(file_exists(PHPHAND_DIR.'/taglib/input/'.$input.'/'.$input.'.def.php')){
			$input_config = $this->data_helper->read(PHPHAND_DIR.'/taglib/input/'.$input.'/'.$input.'.def.php','params');
		}else{
			$input_config = array();
		}
		$this->sign('input',$input);
		$this->sign('input_config',$input_config);
		$this->sign('field_config',$field_config);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display();
	}
}