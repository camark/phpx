<?php
class Form_input_interface_helperModel extends PHPHand_Model
{
	function display($config,$ajax=true,$action='current')
	{
		if($action=='current')
		{
			$action = $_SERVER['REQUEST_URI'];
		}
		$this->view->sign('ajax',$ajax);
		$this->view->sign('action',$action);
		$this->view->sign('config',$config);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->view->display('display');
	}


	function __edit()
	{
		
		$config = $this->query->get('config');
	    $data_model = $this->query->get('model');
	   
        $args_part_index = $this->query->get('part_index');//获得下标
        if($args_part_index!==false) $args_part_index=(int)$args_part_index;
		

		list($t,$idc) = $this->{$data_model}->get_table_and_column();
		$id = $this->query->get($idc);

        //$args_part_index     = intval($this->query->get('part_index'))-1;//获得下标
		$args_part_sub_index = $this->query->get('part_sub_index');//下一级下标
		
		
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
			$this->view->display('part_edit');
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
				if($args_part_index!==false && $part_index !==$args_part_index){continue;}
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
			   if($args_part_index !==false && $part_index !== $args_part_index){continue;}

				$part_data = $data[$part['title']];
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
								$sub_index = -1;
								foreach($part_data as $_sub_index => $part_sub)
								{
									if($part_sub['__namefix__']==$args_part_sub_index)
									{
										$sub_index = $_sub_index;
										break;
									}
								}
								$part_data[$sub_index] = $sub_data;
							}
							else
							{
								$sub_index = sizeof($part_data);
								$part_data[] = $sub_data;
							}
							
						}
					}
				}else{
					$part_data = $this->input->check($part['fields'],$index);
				}
				//echo $part['title'];
				$data[$part['title']] = $part_data;
//print_r($data);exit;
			}
		}
		//die();
		//$data = $this->input->check($config,$index);
		//print_r($data);exit;
		
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
		
	     # ------------ 插件调用-------------#
	     $plugin_resume_tag_photo_is_change = false;
	     if($row['基本信息']['photo'] != $data['基本信息']['photo'])
		 {//如果照片有变化，更新“照片”标记
			$plugin_resume_tag_photo_is_change = true;
		 }
         $plugin_resume_tag_user_id = $row['隐藏部分']['user_id_i'];
		 $plugin_resume_tag_hr_id = $_SESSION['hr_id'];
         $plugin_jobs=$this->plugin_job->get_list('taglib.form.input_interface.__edit.success');
         foreach($plugin_jobs as $job){
                include __ROOT__.$job;
         }
		 # ------------ 插件调用-------------#
		 	
       
		$return_data = array();
		$return_data['status'] = 1 ;
		$return_data['data'] = array('msg' => '成功','part_index'=>$args_part_index,'part_sub_index'=>$args_part_sub_index);
		//$msg = 
		PHPHand_Action::getInstance()->message('操作成功(0)');
	}
}