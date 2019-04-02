<?php
class Form_input_part_interface_edit_helperModel extends PHPHand_Model
{
	function __edit()
	{
		
		$config = $this->query->get('config');
	    $data_model = $this->query->get('model');
	   
        $args_part_index = intval($this->query->get('part_index'))-1;//获得下标
		

		list($t,$idc) = $this->{$data_model}->get_table_and_column();
		$id = $this->query->get($idc);

        $args_part_index     = intval($this->query->get('part_index'))-1;//获得下标
		$args_part_sub_index = $this->query->get('part_sub_index');//下一级下标
		if($args_part_sub_index!==false)
		{
			$args_part_sub_index = intval($args_part_sub_index)-1;
		}
		
		$row = $this->{$data_model}->get($id);
		$row = unserialize($row['data']);
		
		if(sizeof($_POST)==0){
			$this->view->sign('idc',$idc);
			$this->view->sign('idc_val',$id);
			$this->view->sign('part_index',$args_part_index);
			$this->view->sign('part_sub_index',$args_part_sub_index);
			$this->view->sign('row',$row);
			$this->view->sign('table',$config);
			$this->view->setAbsoluteDir(dirname(__FILE__));
			$this->view->display('edit');
			exit;
		}
		$config_array = explode('_',$config);
		
		$config=$this->table_config->read($config);
		$pages=$this->input->serialize($config);
		$page_index = isset($_POST['edit_page'])?$_POST['edit_page']:0;
		if(sizeof($pages)>1 && $page_index<sizeof($pages)-1)
		{
			$page = $pages[$page_index];
			$page_config = $this->input->get_page_config($page);
			foreach($page['parts'] as $part_index => $part){
				if($args_part_index>=0 && $part_index !==$args_part_index){continue;}
				if(isset($part['multi']) && $part['multi'])
				{
					if(!isset($_POST['partflag_'.$page_index.'_'.$part_index]) || !is_array($_POST['partflag_'.$page_index.'_'.$part_index]))
					{
						$this->input->check($part['fields']);
					}else{
						foreach($_POST['partflag_'.$page_index.'_'.$part_index] as $namefix)
						{
							$fields = array();
							foreach($part['fields'] as $field => $config)
							{
								$fields[$field.$namefix] = $config;
							}
							$this->input->check($fields);
						}
					}
				}else{
					$this->input->check($part['fields']);
				}
			}
			$this->ajax_helper->goto_next_page($page_index);
		}
		$index = array();
		$data = $row;
		$part_index_data = array();
		foreach($pages as $page_index => $page)
		{
			foreach($page['parts'] as $part_index => $part)
			{
			   if($args_part_index >=0 && $part_index !== $args_part_index){continue;}
			
				$part_data = $args_part_sub_index >=0 ? $data[$part['title']] : array();
				if(isset($part['multi']) && $part['multi'])
				{
					if(!isset($_POST['partflag_'.$page_index.'_'.$part_index]) || !is_array($_POST['partflag_'.$page_index.'_'.$part_index]))
					{
						$part_data = $this->input->check($part['fields'],$index);
					}else{
						foreach($_POST['partflag_'.$page_index.'_'.$part_index] as $namefix)
						{
							$fields = array();
							foreach($part['fields'] as $field => $config)
							{
								$fields[$field.$namefix] = $config;
							}
							$sub_data = $this->input->check($fields,$index,$namefix);
							$sub_data['__namefix__'] = $namefix;
							if($args_part_sub_index !== false)
							{
								$part_data[$args_part_sub_index] = $sub_data;
							}
							else
							{
								$part_data[] = $sub_data;
							}
							
						}
					}
				}else{
					$part_data = $this->input->check($part['fields'],$index);
				}
				
				$data[$part['title']] = $part_data;
			}
		}
		//die();
		//$data = $this->input->check($config,$index);
		
		$template_id = $config_array[1];
		$index = $this->data_template->get_index($template_id,$index);
		$this->{$data_model}->_update(array(
			'data' => serialize($data),
		),$id);
		$index_id = $_SERVER['HTTP_HOST'].'@'.$template_id.'@'.$id;
		$this->solr->update($template_id,$index_id,$index);
		
		
		if(is_object($this->ajax_helper) && $output=='refresh'){
			$this->ajax_helper->refresh($id);
		}
		$return_data = array();
		$return_data['status'] = 1 ;
		$return_data['data'] = array('part_index'=>$args_part_index,'part_sub_index'=>$args_part_sub_index);
		
		PHPHand_Action::getInstance()->message(json_encode($return_data));
	}

}