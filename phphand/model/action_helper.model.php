<?php
class Action_helperModel extends PHPHand_Model{
	function saddslashes($string) {
	    if (is_array($string)) {
	        foreach ($string as $key => $val) {
	            $string[$key] = $this->saddslashes($val);
	        }
	    } else {
	        $string = addslashes($string);
	    }
	    return $string;
	}
 


	function add($table,$config=null,$return=false){
		if($config==null) $config=$table;
		if(sizeof($_POST)==0){
			
			$this->view->sign('table',$config);
			$this->view->setAbsoluteDir(dirname(__FILE__).'/action_helper/');
			$this->view->display('add');
			exit;
		}

		//GPC过滤
		$magic_quote = get_magic_quotes_gpc();
		if(empty($magic_quote)) {
		    $_POST = $this->saddslashes($_POST);
		}

		//$config = $this->config_helper->get_form_config($table);
		//$config = $this->data_helper->get(__ROOT__.'/dev/config/'.$table.'.php','config');
		$array = $this->input->check($config);
		$id = $this->{$table}->none_pre()->_insert($array);
		//$this->action->form->init($config);
		//$this->action->form->set_db_pre_null()->insert($table);
		if($return) return $id;
		$this->action->message('添加成功('.$id.')',str_replace('&amp;','&',$_POST['phphand_auto_refer']));
	}

	function edit($table,$config=null,$refresh=true){
		$this->{$table}->none_pre();
		list($t,$idc) = $this->{$table}->get_table_and_column();
		
		$id = $this->query->get($idc);
		$row = $this->{$table}->get($id);
		$this->env->set('MAIN_ROW',$row);
		if($config==null) $config=$table;
		if(sizeof($_POST)==0){
			$this->view->sign('row',$row);
			$this->view->sign('table',$config);
			$this->view->setAbsoluteDir(dirname(__FILE__).'/action_helper/');
			$this->view->display('edit');
			exit;
		}

		//GPC过滤
		$magic_quote = get_magic_quotes_gpc();
		if(empty($magic_quote)) {
		    $_POST = $this->saddslashes($_POST);
		}
		
		$array = $this->input->check($config);
		$this->{$table}->_update($array,$id);
		/*$config = $this->config_helper->get_form_config($table);
		$this->action->form->init($config);
		$this->action->form->addTerm($idc,$id,'type{check}');
		$this->action->form->set_db_pre_null()->update($table);*/
		if(is_object($this->ajax_helper) && $refresh){
			$this->ajax_helper->refresh($id);
		}
		$this->action->message('保存成功('.$id.')',urldecode($_POST['phphand_auto_refer']));
	}
	
	function index($table,$template_id=0){
		$this->view->sign('table',$table);
		$this->view->setAbsoluteDir(dirname(__FILE__).'/action_helper/');
		$this->view->sign('___template_id',$template_id);
		$this->view->display('index');
		exit;
	}
	
	function setup($table){
		$this->view->sign('table',$table);
		$this->view->setAbsoluteDir(dirname(__FILE__).'/action_helper/');
		$this->view->display('setup');
		exit;
	}
	
	#虚表的数据添加
	function __add($template_name,$data_model = 'max_data',$append_data = array(),$method='output')
	{
		if(is_int($template_name)){
			$template_id = $template_name;
		}else{
			$template = $this->data_template->get_by('name',$template_name);
			
			$template_id = $template['template_id'];
		}
		$config = $this->data_template->serialize_template_into_config($template_id,true);
		if(sizeof($_POST)==0){
			$this->view->sign('table',$config);
			$this->view->setAbsoluteDir(dirname(__FILE__).'/action_helper/');
			$this->view->display('add');
			exit;
		}
		
		$config=$this->table_config->read($config);
		$pages=$this->input->serialize($config);
		$page_index = isset($_POST['edit_page'])?$_POST['edit_page']:0;
		if(sizeof($pages)>1 && $page_index<sizeof($pages)-1)
		{
			$page = $pages[$page_index];
			$page_config = $this->input->get_page_config($page);
			foreach($page['parts'] as $part_index => $part){
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
		$data = array();
		foreach($pages as $page_index => $page)
		{
			foreach($page['parts'] as $part_index => $part)
			{
				$part_data = array();
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
							$part_data[] = $sub_data;
						}
					}
				}else{
					$part_data = $this->input->check($part['fields'],$index);
				}
				
				$data[$part['title']] = $part_data;
			}
		}
		if($method=='check') return true;
		$index = $this->data_template->get_index($template_id,$index);
		
		$array = array(
			'createdate' => time(),
			'template_id' => $template_id,
			'data' => serialize($data),
		);
		
		if(is_array($append_data))
		{
			$array = array_merge($array,$append_data);
		}
		
		$id = $this->{$data_model}->_insert($array);
		$index['id']=$_SERVER['HTTP_HOST'].'@'.$template_id.'@'.$id;
		$this->solr->create($template_id,$index);
		
		if($method=='return')
		{
			return array($id,$index['id']);
		}
		
		PHPHand_Action::getInstance()->message('创建成功('.$id.')');
	}
	
	
	function __edit($template_name,$data_model='max_data',$method='output')
	{

		if(is_int($template_name)){
			$template_id = $template_name;
		}else{
			$template = $this->data_template->get_by('name',$template_name);
			$template_id = $template['template_id'];
		}
		$config = $this->data_template->serialize_template_into_config($template_id);
		list($t,$idc) = $this->{$data_model}->get_table_and_column();
		$id = $this->query->get($idc);

        $args_part_index = intval($this->query->get('part_index'))-1;//获得下标
		
		$row = $this->{$data_model}->get($id);
		$row = unserialize($row['data']);

		if(sizeof($_POST)==0){
			$this->view->sign('part_index',$args_part_index);
			$this->view->sign('row',$row);
			$this->view->sign('table',$config);
			$this->view->setAbsoluteDir(dirname(__FILE__).'/action_helper/');
			$this->view->display('edit');
			exit;
		}
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
		foreach($pages as $page_index => $page)
		{
			foreach($page['parts'] as $part_index => $part)
			{
			   if($args_part_index>=0 && $part_index !==$args_part_index){continue;}
			
				$part_data = array();
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
							$part_data[] = $sub_data;
						}
					}
				}else{
					$part_data = $this->input->check($part['fields'],$index);
				}
				
				$data[$part['title']] = $part_data;
			}
		}
		//$data = $this->input->check($config,$index);

		$index = $this->data_template->get_index($template_id,$index);

		
		$this->{$data_model}->_update(array(
			'data' => serialize($data),
		),$id);
		$index_id = $_SERVER['HTTP_HOST'].'@'.$template_id.'@'.$id;
		$this->solr->update($template_id,$index_id,$index);

		
		if($method=='return')
		{
			return array($id,$index['id']);
		}
		
		if(is_object($this->ajax_helper) && $output=='refresh'){
			$this->ajax_helper->refresh($id);
		}
		PHPHand_Action::getInstance()->message('保存成功('.$id.')');
	}
	
	#显示Wizards
	function wizard($array,$style='default',$title='')
	{
		$this->view->sign('wizard_array',$array);
		$this->view->sign('wizard_style',$style);
		$this->view->sign('wizard_title',$title);
		$this->view->setAbsoluteDir(dirname(__FILE__).'/action_helper/');
		$this->view->display('wizard');
		exit;
	}
}