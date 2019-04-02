<?php
////////////////////////////////////////////////////////////////////////////////////////
//这个文件，是对一个表单可输入多条数据的部分，
//加载该部分的新输入域
//该输入域的所有输入接口需要重新命名（加上特定的后缀扩展）
//同时标明该扩展，以验证数据的时候用

class Server extends PHPHand_Action
{
	function _default()
	{
		$config = $this->query->get('config');
		$page_index = (int)$this->query->get('page_index');
		$part_index = (int)$this->query->get('part_index');
		
		$this->sign('config_file',$config);
		$this->sign('page_index',$page_index);
		$this->sign('part_index',$part_index);
		
		//新名称后缀
		$namefix = time().rand(1000,9999);
		$this->sign('namefix',$namefix);
		$config = $this->table_config->read($config);
		
		
		$pages = $this->input->serialize($config);
		
		$part = $pages[$page_index]['parts'][$part_index];
		
		$fields = array();
		foreach($part['fields'] as $field => $config)
		{
			$fields[$field.$namefix] = $config;
		}
		$part['fields']=$fields;
		
		$this->sign('part',$part);
		$this->view->setAbsoluteDir(dirname(__FILE__));

		$this->sign('jqueryBasic',true);
		$this->sign('jqueryAjaxPost',true);
		$this->display('get_part');
	}

	
	function part_edit()
	{

		$this->sign('jqueryBasic',true);
		$this->sign('jqueryAjaxPost',true);
		$this->{'form.input_interface.helper'}->__edit();
		/*
		$config = $this->query->get('config');
		$part_index = (int)$this->query->get('part_index');
		$part_sub_index = $this->query->get('part_sub_index');
	    $data_model = $this->query->get('model');
	    $post_action = $this->query->get('post_action');
	    
		list($t,$idc) = $this->{$data_model}->get_table_and_column();
		$id = $this->query->get($idc);
		
        //$args_part_index = intval($this->query->get('part_index'))-1;//获得下标
		
		$row = $this->{$data_model}->get($id);
		$row = unserialize($row['data']);

		$this->view->sign('post_action',$post_action);
		$this->view->sign('idc',$idc);
		$this->view->sign('idc_val',$id);
		$this->view->sign('part_index',$part_index);
		$this->view->sign('part_sub_index',$part_sub_index);
		$this->view->sign('row',$row);
		$this->view->sign('table',$config);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		
		$this->view->display('part_edit');*/

	}
}