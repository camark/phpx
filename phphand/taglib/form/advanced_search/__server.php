<?php
class Server extends PHPHand_Action
{
	function get_input()
	{
		$config_file = $this->query->get('config');
		$field = $this->query->get('field');
		
		
		$config = $this->table_config->read($config_file);
		if(!isset($config[$field]['input']) || $config[$field]['input']=='' || $config[$field]['input']=='none') exit('FALSE');
		$inc = '../../../../data/input/'.$this->input->build($config_file,$field,$config[$field]);

		$this->sign('inc',$inc);
		$this->sign('row',array());
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display('get_input');
	}
}