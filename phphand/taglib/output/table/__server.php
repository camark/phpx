<?php
class Server extends PHPHand_Action
{
	function __construct()
	{
		parent::__construct();
		//if(!$this->config->get('admin_session')) exit;
		//if(!isset($_SESSION[$this->config->get('admin_session')])) exit;
	}

	function update_show_or_hide()
	{
		$show_cols=urldecode($this->query->get('show_cols'));
		$show_cols=explode(',',$show_cols);
		$cf = $this->query->get('config');

		if(!is_dir(__ROOT__.'/host/'.$_SERVER['HTTP_HOST'].'/')) mkdir(__ROOT__.'/host/'.$_SERVER['HTTP_HOST'].'/');
		if(!is_dir(__ROOT__.'/host/'.$_SERVER['HTTP_HOST'].'/output/')) mkdir(__ROOT__.'/host/'.$_SERVER['HTTP_HOST'].'/output');


		$dir = __ROOT__.'/host/'.$_SERVER['HTTP_HOST'].'/output/';
		$config=$this->data_helper->read($dir.$cf.'.php','config');
		if(!$config)
		{
			$config = $this->table_config->read($cf);
		}
		

		foreach($config as $field => $field_config)
		{
			if(in_array($field,$show_cols))
			{
				$config[$field]['list_show']=1;
			}else{
				unset($config[$field]['list_show']);
			}
		}
		
		
		$this->data_helper->write($dir.$cf.'.php',$config,'config');
	}
	
	function switch_mode()
	{
		$flag=$this->query->get('flag');
		if(!file_exists(dirname(__FILE__).'/cache/'.$flag.'.php'))
		{
			$config=array(
				'mode' => 'comp',
				'pagesize' => 20,
			);
		}else{
			$config = $this->data_helper->read(dirname(__FILE__).'/cache/'.$flag.'.php','config');
			if($config['mode']=='table')
				$config['mode']='comp';
			else
				$config['mode']='table';
		}
		$this->data_helper->write(dirname(__FILE__).'/cache/'.$flag.'.php',$config,'config');
	}
	
	function change_pagesize()
	{
		$flag = $this->query->get('flag');
		$pagesize=$this->query->get('pagesize');


		if(!file_exists(dirname(__FILE__).'/cache/'.$flag.'.php'))
		{
			$config=array(
				'mode' => 'table',
				'pagesize' => $pagesize,
			);
		}else{
			$config = $this->data_helper->read(dirname(__FILE__).'/cache/'.$flag.'.php','config');
			$config['pagesize']=$pagesize;
		}
		
		$this->data_helper->write(dirname(__FILE__).'/cache/'.$flag.'.php',$config,'config');
	}
	
	function get_input()
	{
		$value = urldecode(str_replace('****','--',$this->query->get('value')));
		$config_file = $this->query->get('config');
		$field = $this->query->get('field');
		
		$config = $this->table_config->read($config_file);
		if(!isset($config[$field]['input']) || $config[$field]['input']=='' || $config[$field]['input']=='none') exit('FALSE');
		$inc = '../../../../data/input/'.$this->input->build($config_file,$field,$config[$field]);

		$this->sign('inc',$inc);
		$this->sign('row',array($field=>$value));
		$this->sign('ajax_update',true);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display('get_input');
	}
	
	function update_column()
	{
		$table = $this->query->get('table');
		$config_file = $this->query->get('config');
		$field = $this->query->get('field');
		$value = urldecode(str_replace('****','--',$this->query->get('value')));
		$data_id=$this->query->get('data_id');
		$is_virtual_field = $this->query->get('is_virtual_field');
		
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$table).'_id';
		
		
		$content = file_get_contents(dirname(__FILE__).'/log.txt');
		$content .= "\r\n".date('Y-m-d H:i:s'). '   ' . session_id() . "              " .$_SERVER['REQUEST_URI'];
		file_put_contents(dirname(__FILE__).'/log.txt',$content);

		$config = $this->table_config->read($config_file);
		
		
		$field_config = $this->input->get_example_detail($config[$field]);
		$this->sign('field_config',$field_config);
		$checker = $this->input->load_checker($field_config['input']);
		
		$_POST[$field]=$value;
		if(false!=$checker)
		{
			$value = $checker->check($field,$config);
		}else{
			$value = $_POST[$field];
		}

		
		$this->sign('rst',array($field=>$value,$id_column=>$data_id));

		#方法下插件钩子#
		$plugin_jobs=$this->plugin_job->get_list('tag.output.table.updatecolumn');
		foreach($plugin_jobs as $job){
			include __ROOT__.$job;
		}
		
		if($is_virtual_field=='true'){
			/*$pages = $this->input->serialize($config);
			$data = $this->{$table}->none_pre()->get($data_id);
			$data = unserialize($data['data']);
			if(!$data) $data=array();
			if(sizeof($pages)==1 && sizeof($pages[0]['parts'])==1)
			{
				//如果是的数据结构，直接按照扁平方式序列化保存
				$data[$field]=$value;
				$this->{$table}->none_pre()->_update(array(
					'data' => serialize($data),
				),$data_id);
			}else{
				//如果是复杂的数据结构，保存数据的时候要讲究数据的逻辑层次
				$this->save_tree($data,$field,$value);
			}*/
			
			$this->data_template->set_value($table,$data_id,$field,$value);
		}else{
			$this->{$table}->none_pre()->_update(array(
				$field => $value,
			),$data_id);
		}
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->sign('field',$field);
		$this->display('update_column');
	}
	
	function save_tree($data,$field,$value)
	{
		foreach($data as $key => $val)
		{
			if(is_array($val))
			{
				$data[$key] = $this->save_tree($val,$field,$value);
			}else{
				if($key==$field)
				{
					$data[$key] = $value;
					break;
				}
			}
		}
		return $data;
	}
	
	function get_sub_data()
	{
		$config = $this->query->get('config');
		$id = $this->query->get('id');
		$fid = $this->query->get('fid');
		$table = $this->query->get('table');
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$table).'_id';
		$reflector = $this->query->get('reflector');
		
		$sql_cache = $this->query->get('sql_cache');
		$sql = $this->data_helper->read(__ROOT__.'/data/sql/'.$sql_cache.'.php');
		if(!$sql)
		{
			exit($sql_cache);
		}
		$level = intval($this->query->get('level'));
		if(!$level) $level=0;
		if(!$id) $level++;
		
		$cfg = $this->table_config->read($config);
		if(is_string($sql)) $sql = preg_replace("/ORDER BY.+?$/is",'',$sql);
		$fid_column = '';
		$title_column = '';
		$n = 0;
		foreach($cfg as $field => $field_config)
		{
			$n++;
			if(isset($field_config['as']) && $field_config['as']=='as_fid_column')
			{
				$fid_column=$field;
			}else if(isset($field_config['as']) && $field_config['as']=='as_title_column')
			{
				$title_column = $field;
			}
		}
		
		if(!$id){
			if(!$fid_column) exit('');
			if(preg_match("/`?$fid_column`?=.+?( |$)/is",$sql))
			{
				$sql = preg_replace("/`?$fid_column`?=.+?( |$)/is","`$fid_column`=$fid\\1",$sql);
			}else if(strpos($sql,"WHERE")>0 || strpos($sql,"where")>0){
				$sql.=" AND `$fid_column`=$fid";
			}else{
				$sql.=" WHERE `$fid_column`=$fid";
			}
		}else{
			if(is_array($sql)){
				$sql = "SELECT * FROM $table";
				$this->sign('is_virtual_table',true);
			}else{
				if(preg_match("/`?$fid_column`?=.+?( |$)/is",$sql))
				{
					$sql = preg_replace("/`?$fid_column`?=.+?( |$)/is","`$fid_column`=$fid",$sql);
				}
			}
			if(strpos($sql,"WHERE")>0 || strpos($sql,"where")>0){
				$sql.=" AND `$id_column`=$id";
			}else{
				$sql.=" WHERE `$id_column`=$id";
			}
		}
		
		$this->sign('reflector',$reflector);
		$this->sign('update_table',$table);
		$this->sign('fid',$fid);
		$this->sign('config_file',$config);
		$this->sign('level',$level);
		$this->sign('id_column',$id_column);
		$this->sign('sql',$sql);
		$this->sign('title_column',$title_column);
		$this->sign('fid_list_column',$fid_column);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display('get_sub_data');
	}
	
	function save_list_config()
	{
		$config = $this->query->get('config');
		if(strpos($config,'$template_list_')===0)
		{
			$template_id = (int)substr($config,strlen('$template_list_'));
			
			$fids = $this->data_template->get_fid_chain($template_id);
			
			foreach($_POST as $name => $val)
			{
				if(strpos($name,'width_')===0)
				{
					$key = substr($name,6);
					$row = $this->data_template_item->get_by(array(
						'template_id' => $fids,
						'key' => $key,
					));
					
					$list = unserialize($row['list']);
					$list['list_width'] = $val;
					$this->data_template_item->_update(array(
						'list' => serialize($list),
					),$row['item_id']);
				}else if(strpos($name,'arrange_order_')===0)
				{
					$key = substr($name,14);
					$row = $this->data_template_item->get_by(array(
						'template_id' => $fids,
						'key' => $key,
					));
					$list = unserialize($row['list']);
					$list['list_arrange_order'] = $val;
					$this->data_template_item->_update(array(
						'list' => serialize($list),
					),$row['item_id']);
				}
			}
			#删除缓存
			unlink(__ROOT__.'/host/'.$_SERVER['HTTP_HOST'].'/config/'.$config.'.php');
			unlink(__ROOT__.'/host/'.$_SERVER['HTTP_HOST'].'/output/'.$config.'.php');
		}else{
			$config = $this->table_config->read($config);
			foreach($_POST as $name => $val)
			{
				if(strpos($name,'width_')===0)
				{
					$key = substr($name,6);
					$config[$key]['list_width']=$val;
				}else if(strpos($name,'arrange_order_')===0)
				{
					$key = substr($name,14);
					$config[$key]['list_arrange_order'] = $val;
				}
			}
			print_r($config);
			$this->table_config->write($this->query->get('config'),$config);
		}
	}
}