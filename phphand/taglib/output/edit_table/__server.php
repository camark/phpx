<?php
class Server extends PHPHand_Action
{
	function add()
	{
		$config = $this->query->get('config');
		$update_table = $this->query->get('update_table');
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$update_table).'_id';
		
		
		$array = $this->input->check($config);
		
		$id = $this->{$update_table}->none_pre()->_insert($array);
		$rs = $this->{$update_table}->none_pre()->get($id);
		if(isset($_SESSION['debugger']) && $_SESSION['debugger']=='phphand')
		{
			exit($id);
		}
		
		
		$default_set = unserialize($_POST['serialized_data']);
		$this->sign('default_set',$default_set);
		$this->sign('config_file',$config);
		$config = $this->table_config->read($config);
		$this->sign('rst',$rs);
		$this->sign('config',$config);
		$this->sign('id_column',$id_column);
		$this->sign('id',$id);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display('add_success');
	}
	
	
	function remove()
	{
		$update_table = $this->query->get('update_table');
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$update_table).'_id';
		
		$this->{$update_table}->none_pre()->_delete(array($id_column=>$this->query->get('data_id')));
	}
	
	function save()
	{
		$config_file = $this->query->get('config');
		$update_table = $this->query->get('update_table');
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$update_table).'_id';
		$config = $this->table_config->read($config_file);
		$updates = array();
		
		$postdata = $_POST;
		
		if(isset($postdata['ids'])){
			foreach($postdata['ids'] as $id)
			{
				$_POST = array();
				
				
				foreach($config as $ck => $cv)
				{
					if(isset($postdata[$ck.'__'.$id]))
						$_POST[$ck] = $postdata[$ck.'__'.$id];
				}
				
				$array = $this->input->check($config);
				
				$updates[$id] = $array;
			}
		}
		foreach($updates as $id => $array)
		{
			$this->{$update_table}->none_pre()->_update($array,$id);
		}
		echo '保存成功';
	}
}