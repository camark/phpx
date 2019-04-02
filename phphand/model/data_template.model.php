<?php
/****************************************************************************************
 * Data_template 类
 * 这是一个关键类
 * 这个类与数据模型的配置有关，是针对“虚表”的配置相关的一组辅助方法
 * 实表的配置生成和调用等不会调用到本类的方法
 */

class Data_templateModel extends PHPHand_Model
{
	/**
	* 这个方法是将max_data_template_item表中存储的数据模型配置转换为与文本存储的配置文件同等的格式
	* 为什么要做这个格式转换呢？
	* 实际上文本格式存储的配置文件，也是没有表现出层级关系的
	* 但是文本配置的那种数据格式，可以提交给input->serialize,即可转换为具有层级关系的配置形式了
	* 因此将数据库中存储的配置，通过本方法转换为文本同等格式后，再提交给input->serialize，即可转换为有效的具有层级关系的配置形式了
	*/
	function serialize_template_into_config($template_id,$rewrite=false)
	{
		$template_id = intval($template_id);
		$save_name = '$template_'.$template_id;
		$save = $this->table_config->get_path($save_name);
		$hasLevel1 = false;

		if(!file_exists($save) || $rewrite){
			$config = array();
			//获得所有的父模板id
			$fid_array = $this->get_fid_chain($template_id);
			$where_template_id = '1';
			if(count($fid_array)>0)
			{
				$template_id_str = implode(',',$fid_array);
				$where_template_id = "template_id in ({$template_id_str})";
			}
			else
			{//查询本身的
				$where_template_id = "template_id={$template_id}";
			}
			if($this->client->is_mobile())
			{
				$client_sql = " AND client_type IN (0,2)";
			}else{
				$client_sql = " AND client_type IN (0,1)";
			}
			//echo "SELECT * FROM max_data_template_item WHERE {$where_template_id} AND fid=0 ORDER BY `order` ASC";
			$query = $this->db->query("SELECT * FROM max_data_template_item WHERE {$where_template_id}{$client_sql} AND fid=0 ORDER BY `order` ASC");
			while($rs1 = mysql_fetch_assoc($query))
			{
				if($rs1['key'] && $this->data_template_item->count("fid=".$rs1['item_id'])==0)
				{
					$field_config=$this->get_field_config($rs1);
					$key = $rs1['key'];
					//if(!$key) $key = $rs1['title'];
					$config[$key] = $field_config;
					continue;
				}
				$config[$rs1['title']] = array('level' => 2,'type'=>$rs1['type']);
				$query2=$this->db->query("SELECT * FROM max_data_template_item WHERE fid=" . $rs1['item_id']."{$client_sql} ORDER BY `order` ASC");
				while($rs2=mysql_fetch_assoc($query2)){
					if($rs2['key'] && $this->data_template_item->count("fid=".$rs2['item_id'])==0)
					{
						if($rs1['fid']==0 && $hasLevel1){
							$config['#'.$rs1['item_id']]=array('level'=>2,'type'=>$rs1['type']);
							$config[$rs1['title']]['level'] = 1;
						}
						$field_config=$this->get_field_config($rs2);
						$config[$rs2['key']] = $field_config;
						continue;
					}
					$config[$rs2['title']] = array('level' => 2,'type'=>$rs2['type']);
					$config[$rs1['title']]['level'] = 1;
					$hasLevel1 = true;
					$query3=$this->db->query("SELECT * FROM max_data_template_item WHERE fid=" . $rs2['item_id']."{$client_sql} ORDER BY `order` ASC");
					while($rs3=mysql_fetch_assoc($query3)){
						$field_config=$this->get_field_config($rs3);
						$config[$rs3['key']]=$field_config;
					}
					/*
					if($rs1['key'] && $this->data_template_item->count("fid=".$rs1['item_id'])==0)
					{
						$field_config=$this->get_field_config($rs1);
						$config[$rs1['key']] = $field_config;
						continue;
					}*/
				}
				
				//$config[] = array('title'=>$part['title'],'fields' => $part_config);
			}
			
			$return = array();
			foreach($config as $key => $field_config)
			{
				if(isset($field_config['level']))
				{
					$hide = false;
					switch($field_config['type'])
					{
						case 3:
							$double = true;
							$multi = false;
							break;
						case 4:
							$double = false;
							$multi = true;
							break;
						case 5:
							$double = true;
							$multi = true;
							break;
						case 7:
							$hide = true;
						default:
							$double = false;
							$multi = false;
					}
					if($field_config['level']==1)
					{
						$return['{'.$key.'}']=array('double' => $double,'multi'=>$multi,'hide' => $hide);
					}else{
						$return['['.$key.']']=array('double' => $double,'multi'=>$multi,'hide' => $hide);
					}
				}else{
					$return[$key]=$field_config;
				}
			}
			$this->table_config->write($save_name,$return);
		}
		return $save_name;
	}
	
	function get_field_config($rs)
	{
		if(!$rs['list'])
		{
			$list_config=array();
		}else{
			$list_config=unserialize($rs['list']);
		}
		
		if(!$rs['config'])
		{
			$input_config=array();
		}else{
			$input_config=unserialize($rs['config']);
		}
		$field_config=array_merge(array(
			'showname' => $rs['title'],
			'tip' => $rs['tip'],
			'is_virtual_field' => true,
			'detail_output' => $rs['detail_output'],
			'client_type' => $rs['client_type'],
		),$list_config,$input_config);
		
		return $field_config;
	}
	
	function get_list_config($template_id)
	{
		$config = array();
		
	    	//获得所有的父模板id
		$fid_array = $this->get_fid_chain($template_id);
		$where_template_id = '1';
		if(count($fid_array)>0)
		{//有父类的，查询本身和所有父类的
			$fid_array[] = $template_id;
			$template_id_str = implode(',',$fid_array);
			$where_template_id = "template_id in ({$template_id_str})";
		}
		else
		{//查询本身的
			$where_template_id = "template_id={$template_id}";
		}
			
		$query = $this->db->query("SELECT * FROM max_data_template_item WHERE {$where_template_id} AND type=1 ORDER BY `order` ASC");
		while($rs1 = mysql_fetch_assoc($query))
		{
			$field_config=$this->get_field_config($rs1);
			if(!isset($field_config['list_arrange_order']))
			{
				$field_config['list_arrange_order']  = $rs1['order'];
			}
			$config[$rs1['key']]=$field_config;
		}
		
		uasort($config,array('Data_templateModel','list_config_order'));

		$config_name='$template_list_'.$template_id;
		$this->table_config->write($config_name,$config);
		return $config_name;
	}
	
	static function list_config_order($a,$b)
	{
		if(!isset($a['list_arrange_order']) && !isset($b['list_arrange_order'])) return 0;
		if(!isset($a['list_arrange_order']) && isset($b['list_arrange_order'])) return -1;
		if(isset($a['list_arrange_order']) && !isset($b['list_arrange_order'])) return 1;
		if((int)$a['list_arrange_order']==(int)$b['list_arrange_order']) return 0;
		if((int)$a['list_arrange_order']>(int)$b['list_arrange_order']) return 1;
		return -1;
	}
	
	
	function get_plain_data($data,&$plain=array())
	{
		foreach($data as $key => $val)
		{
			if(is_array($val) && !$this->is_simple_array($val)){
				$this->get_plain_data($val,$plain);
				continue;
			}
			if(!isset($plain[$key]))
			{
				$plain[$key]=$val;
				continue;
			}
			if(is_array($val))
			{
				if(is_array($plain[$key])) $plain[$key] = array_merge($plain[$key],$val);
				else{
					if(!in_array($plain[$key],$val)) $val[]=$plain[$key];
					$plain[$key]=$val;
				}
				continue;
			}
			if(!isset($plain[$key]))
			{
				$plain[$key] = $val;
			}else{
				if(preg_match('/_i$/i',$key))
				{
					if(is_array($plain[$key])){
						if(!in_array($val,$plain[$key])) $plain[$key][] = $val;
					}else if($val!=$plain[$key]){
						$plain[$key]=array($plain[$key],$val);
					}
				}else if(preg_match('/_is$/i',$key))
				{
					if(is_array($plain[$key])){
						if(!in_array($val,$plain[$key])) $plain[$key][] = $val;
					}else if($val!=$plain[$key]){
						$plain[$key]=array($plain[$key],$val);
					}
				}else{
					$plain[$key].=','.$val;
				}
			}
		}
	}
	
	function is_simple_array($array)
	{
		foreach($array as $key => $val)
		{
			if(!is_int($key)) return false;
		}
		return true;
	}
	
	/**
	* 将data表示的可存储索引的形式，转换为扁平的索引形式，
	* 以提供给SOLR引擎进行存储
	*/
	function get_index($template_id,$data)
	{
		$index=array();
		$index['template_i'] = $template_id;
		$fids = $this->get_fid_chain($template_id);
		$query = $this->db->query("SELECT * FROM max_data_template_item WHERE template_id IN (".join(',',$fids).") ORDER BY `order` ASC");
		$keyword = '';
		while($rs1 = mysql_fetch_assoc($query))
		{
			if($rs1['key'])
			{
				$key = $rs1['key'];
				$keyword .= isset($data[$key]) && !empty($data[$key]) ? $data[$key] : '';
				if(preg_match('/_i$/is',$key))
				{
					if(isset($data[$key])){
						if(is_array($data[$key])){
							#类似salary_i那种范围形式的
							//$index[$key] = $data[$key];
							foreach($data[$key] as $data_item_key => $data_item_val)
							{
								$index[$data_item_key] = (int)$data_item_val;
							}
						}else
							$index[$key] = (int)$data[$key];
					}
				}else if(preg_match('/_is$/is',$key))
				{
					if(!is_array($data[$key]))
					{
						$data[$key] = explode(',',$data[$key]);
					}
					if(!isset($index[$key])) $index[$key] = array();
					foreach($data[$key] as $item){
						$item=trim($item);
						if($item!=='')
							$index[$key][] = (int)$item;
					}
				}else if(preg_match('/_s$/is',$key))
				{
					$index[$key] = $data[$key];
				}else if(preg_match('/_ss$/is',$key))
				{
					if(!is_array($data[$key]))
					{
						$data[$key] = explode(',',$data[$key]);
					}
					if(!isset($index[$key])) $index[$key] = array();
					foreach($data[$key] as $item){
						$item=trim($item);
						if($item)
							$index[$key][] = $item;
					}
				}
			}
		}

		$index['keyword_s'] = $keyword;
		return $index;
	}
	
	//根据配置输出
	function output($template_id,$key,$value,$store=array())
	{
		$fids = $this->get_fid_chain($template_id);
		$item = $this->data_template_item->get_by(array(
			'template_id' => $fids,
			'key' => $key,
		));
		if(!$item)
		{
			echo $value;
			return;
		}
		$field_config = $this->input->get_example_detail($this->get_field_config($item));
		//if(!isset($field_config['list']) || $field_config['list']=='') $field_config['list']='text';
		//$value = $this->input->get_value($rst,$key);
		if(!isset($field_config['input']) || !$field_config['input'] || $field_config['input']=='none' || !file_exists(__ROOT__.'/phphand/taglib/input/'.$field_config['input'].'/__output.php'))
		{
			echo $value;
			return;
		}
		$field_config['list']='input';
		if(empty($store)) $store=array($key => $value);
		$this->table_config->output($key,$field_config,$store);
	}
	
	function delete_item($item_id)
	{
		$query = $this->db->query("SELECT * FROM max_data_template_item WHERE fid=$item_id");
		while($rs=mysql_fetch_assoc($query))
		{
			$this->delete_item($rs['item_id']);
		}
		$this->max_data_template_item->_delete(array('item_id'=>$item_id));
	}
	

	/**
	 * 根据$template_id获得所有父模板id
	 * @param $template_id
	 * @param $fid_array
	 */
	function get_fid_chain($template_id=0)
	{ 
		$array = array($template_id);
		while($template_id)
		{
			$template = $this->get($template_id);
			if($template){
				$template_id = $template['fid'];
				$array[] = $template_id;
			}else{
				$template_id = 0;
			}
		}
		
		return $array;
	}
    /**
     * 根据模板id获得最顶级的父模板id
     * @param $template_id
     */
	function get_top_fid($template_id=0)
	{
	    $template_id = intval($template_id);
		if($template_id<=0)
		{
			return 0;
		}
		$rs = $this->db->getOne("select template_id,fid from `max_data_template` where template_id={$template_id}");
		$fid = $rs['fid']; 
		$template_id = $rs['template_id'];
		if($fid>0)
		{
			$template_id = $this->get_top_fid($fid);
		}
		return $template_id;
	}
	/**
	 * 获得所有子类
	 * @param $template_id
	 */
	function get_child($template_id=0)
	{
	    $template_id = intval($template_id);
		if($template_id<=0)
		{
			return array();
		}
		$sql = "SELECT * FROM max_data_template WHERE fid=$template_id"; 
		$query = $this->db->query($sql);
		if(empty($query))
		{
			return array();
		}
		$rs = array();
		while($rs_v = mysql_fetch_assoc($query))
		{
			$rs[] = $rs_v;
		}
		return $rs;
		
	}
	
	function get_value($store_table,$data_id,$key,$method='')
	{
		$data = $this->{$store_table}->get($data_id,'data');
		$data = unserialize($data);
		return $this->input->get_value($data,$key,$method);
	}
	
	
	function set_value($store_table,$data_id,$key,$value,$method='')
	{
		$row = $this->{$store_table}->none_pre()->get($data_id);
		if(!$row) return;
		$template_id = $row['template_id'];
		if(!$template_id) return;
		
		$data = unserialize($row['data']);
		$fids = $this->get_fid_chain($template_id);
		
		if(!is_array($key)){
			$array = array(
				$key => array('value' => $value,'method' => $method),
			);
		}else{
			$array = $key;
		}
		
		$index = array();
		foreach($array as $key => $value)
		{
			if(is_array($value))
			{
				$value = $value['value'];
				$method = $value['method'];
			}else{
				$method = '';
			}
			if($method=='ADD-ITEM' || $method=='DELETE-ITEM')
			{
				$old_value = $this->input->get_value($data,$key,'explode');
				if($method=='ADD-ITEM')
				{
					if(!in_array($value,$old_value)) $old_value[]=$value;
				}else{
					foreach($old_value as $fkey => $fval)
					{
						if($fval==$value) unset($old_value[$fkey]);
					}
				}
				
				$value = implode(',',$old_value);
			}
			$item = $this->data_template_item->get_by(array(
				'template_id' => $fids,
				'key' => $key,
			));
			$this->input->set_value($data,$key,$value);
			if(preg_match('/_(i|is|s)$/i',$key))
				$index[$key]=$value;
		}
		


		
		
		//$index = $this->get_index($template_id,$data);
		$this->{$store_table}->none_pre()->_update(array(
			'data' => serialize($data),
		));
		
		$this->solr->update($template_id,$_SERVER['HTTP_HOST'].'@'.$template_id.'@'.$data_id,$index);
	}
	
	function get_data_by($template_id,$key,$value)
	{
		$array = $this->solr->search($template_id,array(
			$key => $value,
		));
		
		if(!isset($array['response']['docs'])) return array();
		return $array['response']['docs'];
	}


	#查询
	function query($table,$template_id,$search_data)
	{
		$search_array = $this->solr->search($template_id,$search_data);
		$main_data = array();
		if(isset($search_array['grouped']))
		{
			foreach($search_array['grouped'] as $group_key => $group_return)
			{
				foreach($group_return['groups'] as $group)
				{
					$search_id = explode('@',$group['doclist']['docs'][0]['id']);
					@$search_id = $search_id[2];
					
					$output_table_search_data = $this->{ $table } ->get($search_id);
					if($output_table_search_data){
						$data = array();
						$this->get_plain_data(unserialize($output_table_search_data['data']),$data);
						$main_data[] = array_merge($output_table_search_data,$data);
					}
				}
				break;
			}
		}else{
			foreach($search_array['response']['docs'] as $search_doc)
			{
				$search_id = explode('@',$search_doc['id']);
				@$search_id = $search_id[2];
				$output_table_search_data = $this->{ $table } ->get($search_id);
				if($output_table_search_data){
					$data = array();
					$this->get_plain_data(unserialize($output_table_search_data['data']),$data);
					$main_data[] = array_merge($output_table_search_data,$data);
				}
			}
		}

		return $main_data;

	}

	#统计
	function count($template_id,$search_data)
	{
		$search_array = $this->solr->search($template_id,$search_data);
		$main_data = array();
		if(isset($search_array['grouped']))
		{
			$count = 0;
			foreach($search_array['grouped'] as $group_key => $group_return)
			{
				$count += $group_return['matches'];
			}
			return $count;
		}else{
			return $search_array['response']['numFound'];
		}
	}
	
}