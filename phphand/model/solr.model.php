<?php
/*
solr 多条件查询
http://localhost:8983/solr/new_core/select?q=id:2*+AND+a1_i:[1+TO+100]&wt=json&indent=true
*/
//define('SOLR_PATH','http://192.168.110.192:8983/solr');
//define('SOLR_PATH','http://210.51.163.178:8983/solr');


class SolrModel extends PHPHand_Model
{
	function search($template_id,$args,$page=1,$pagesize=20)
	{
		$solr_url = $this->get_solr_url($template_id);
		if(empty($solr_url))
		{
			return array();
		}
        
		$str = $solr_url.'select?';
		$match='q=';
		$sort = '';
		$group='';
		foreach($args as $key => $value)
		{
			//有prefix$前缀的，说明是补充修饰符
			if(strpos($key,'prefix$')===0) continue;
			if(isset($_GET['prefix$'.$key]) && $_GET['prefix$'.$key]=='BETTER') continue;
			//值为0，是需要做查询的
			if( (!$value && $value!=0) || !$key) continue;
			if($value==='') continue;
			
			if(strpos($key,'~')===0)
			{
				$key = substr($key,1);
				$sort = '&sort='.$key.urlencode(' ').$value;
				continue;
			}
			if(strpos($key,'@')===0)
			{
				$key = substr($key,1);
				$group = '&group=true&group.field='.$key.'&group.limit=10';
				continue;
			}
			
			if($match!='q=') $match.='+AND+';
			
			//区间查询 begin
			if(strpos($key,'_begin') !== false)
			{
				$key_real = substr($key,0,-6);
				$match .= "{$key_real}:[{$value}+TO+*]";
			}//区间查询 begin
			else if(strpos($key,'_end') !== false)
			{
				$key_real = substr($key,0,-4);
				$match .= "{$key_real}:[*+TO+{$value}]";
			}//OR关系查询
			else if(strpos($value,'%2C') !== false || strpos($value,',')!==false)
			{
				$value = str_replace('%2C',',',$value);
				$match_or = "";
				$val_or_array = explode(',',$value);
				if(isset($_GET['prefix$'.$key]) && $_GET['prefix$'.$key]=='NOT'){
					$match.=$key.':(*';
					foreach($val_or_array as $key_or=>$val_or)
					{
						$match .= "+AND+NOT+{$val_or}";
					}
					$match.=')';
				}else if(isset($_GET['prefix$'.$key]) && $_GET['prefix$'.$key]=='AND'){
					foreach($val_or_array as $key_or=>$val_or)
					{
						if($key_or>0)
						{
							$match_or .= "+AND+";
						}
						if(strpos($val_or,'!!!')===0)
						{
							$val_or = substr($val_or,3);
							$match_or .= "+NOT+";
						}
						$match_or .= "{$key}:*{$val_or}*";
					}
					$match .= "({$match_or})";
				}else{
					foreach($val_or_array as $key_or=>$val_or)
					{
						if($key_or>0)
						{
							$match_or .= "+OR+";
						}
						$match_or .= "{$key}:{$val_or}";
					}
					$match .= "({$match_or})";
				}
			}
			else{
				$match.=$key.':';
				if(is_string($value) && preg_match('/_s$/i',$key))
				{
					if(isset($_GET['prefix$'.$key]) && $_GET['prefix$'.$key]=='NOT')
					{
						$match.='(*+NOT+*'.$value.'*)';
					}elseif(isset($_GET['prefix$'.$key]) && $_GET['prefix$'.$key]=='AND')
					{
						if(strpos($value,'!!!')===0)
							$match.='(*+NOT+*'.substr($value,3).'*)';
						else
							$match.='(*'.$value.'*)';
					}else{
						$match.='*'.$value.'*';
					}
				}else{
					if(isset($_GET['prefix$'.$key]) && $_GET['prefix$'.$key]=='NOT')
					{
						$match.='(*+NOT+'.$value.')';
					}else{
						$match.=$value;
					}
				}
			}
		}
		if($match=='q=') $match.='*:*';
		$str.=$match;
		//$str.='&facet=true&facet.field=user_id_i';
		//$str.='&group=true&group.field=user_id_i&group.limit=10';
		$str.=$sort . $group;
		$str.='&start='.($pagesize*($page-1));
		$str.='&rows='.$pagesize;
		$str.='&wt=json&indent=true';

		if(isset($_SESSION['debugger']) && $_SESSION['debugger'])
		{
			echo '<div style="border:1px solid #060;background:#080;color:white;">'.$str.'</div>';
		}

		$this->snoopy->submit($str);

		$array = $this->std_class_object_to_array(json_decode($this->snoopy->results));
		
		return $array;
	}
	
	function create($template_id,$data)
	{
	    $solr_url = $this->get_solr_url($template_id);
        if(empty($solr_url))
        {
        	return false;
        }
        
		$target_url = $solr_url.'update?wt=json';
		$doc = json_encode($data);
		$doc = preg_replace('/"_version_":"(.+?)"/is','"_version_":\\1',$doc);

		//file_put_contents(__ROOT__.'/a.php',$doc);
		$this->snoopy->submit($target_url,array('stream.body' => '{"add":{ "doc":'.$doc.',"boost":1.0,"overwrite":true,"commitWithin":1000}}'));
		$result = $this->snoopy->results;
		//echo $result;
		$array1 = $this->std_class_object_to_array(json_decode($result));
		//{"responseHeader":{"status":0,"QTime":3}}  status的值： 0:表示成功；其他值表示失败。
		if($array1['responseHeader']['status']==0)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
	function update($template_id,$id,$array)
	{
	    $solr_url = $this->get_solr_url($template_id);
        if(empty($solr_url))
        {
        	return false;
        }
        //unset($array['template_i']);
        
		//http://localhost:8983/solr/resume_core/select?q=id:15&wt=json&indent=true
		if(strpos($id,'@')===false)
		{
			$id = $_SERVER['HTTP_HOST'].'@'.$template_id.'@'.$id;
		}
		$target_url = $solr_url.'select?q=id:'.$id.'&wt=json&indent=true';
		$this->snoopy->fetch($target_url);
		$result = $this->snoopy->results;
		//$result = preg_replace('/"_version_":(.+?)"/is','"_version_":\\1',$result);
		//exit($result);
		$array1 = $this->std_class_object_to_array(json_decode($result));
		if(isset($array1['response']['docs'][0]))
		{
			$doc = $array1['response']['docs'][0];
			unset($doc['_version_']);
			foreach($array as $key => $value)
			{
				$doc[$key] = $value;
			}
			$this->create($template_id,$doc);
		}else{
			$doc = $array;
			$doc['id']=$id;
			$this->create($template_id,$doc);
		}
	}

  /**
   * 根据模板id获得索引的url
   * @param $template_i
   */
	function get_solr_url($template_i=0)
	{
		$template_i = intval($template_i);
		if($template_i<=0)
		{
			return "";
		}
		
		//根据模板id获得顶级父模板id,根据顶级父模板来获得索引
		$top_fid = $this->data_template->get_top_fid($template_i);
	    $template = $this->data_template->get_by('template_id',$top_fid);
        $template_name = $template['name'];
	    //获取索引文件夹
	    $index_dir_name = $_SERVER['HTTP_HOST'].'_'.base64_encode($template_name);
	    
	    if(empty($index_dir_name))
	    {
	    	return "";
	    }
	    
	    //检查core的状态
	    $core_status = $this->core_status($index_dir_name);
	    if(!$core_status)
	    {//如果没有此core，则创建
	    	$this->core_create($index_dir_name);
	    }
		//$index_dir_name = 'new_core';
	    $str = $this->config->get('solr')."solr/{$index_dir_name}/";
	    return $str;
	}
	
	/**
	 * 得到索引中core的状态
	 * @param $core_name
	 */
	function core_status($core_name)
	{
		if(empty($core_name))
		{
			return false;
		}
		//查看core的状态
		$url = $this->config->get('solr')."solr/admin/cores?action=status&core={$core_name}&wt=json";
		$this->snoopy->fetch($url);
		$result = $this->snoopy->results;
		$result = $this->std_class_object_to_array(json_decode($result));
		if(!empty($result['status'][$core_name]))
		{
            return true;		 	
		}
		return false;
	}
	/**
	 * 创建core
	 * @param $core_name
	 */
	function core_create($core_name)
	{
		//return true;
	    if(empty($core_name))
		{
			return false;
		}
		//先copy文件夹
		$copy_url = $this->config->get('solr')."solrhelp/indexDir?d={$core_name}";
		$this->snoopy->fetch($copy_url);
		$copy_result = $this->snoopy->results;
		$copy_result = $this->std_class_object_to_array(json_decode($copy_result));
		$copy_result_status = isset($copy_result['status']) ? intval($copy_result['status']) : -1;
		if($copy_result_status==0)
		{//$core_name的目录已经solr服务器上存在
			return true;
		}
		else if($copy_result_status<0)
		{//$core_name的目录创建失败
			return false;
		}
		
		//配置core
		$url = $this->config->get('solr')."solr/admin/cores?action=create&name={$core_name}&instanceDir={$core_name}&dataDir=data&config=solrconfig.xml&schema=schema.xml&wt=json";
		$this->snoopy->fetch($url);
		$result = $this->snoopy->results;
		$result = $this->std_class_object_to_array(json_decode($result));
		//var_dump($result);
		return true;
	}
   function std_class_object_to_array($stdclassobject)
	{
		$_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;
		if(!is_array($_array)) $_array = array();
		$array = array();
		foreach ($_array as $key => $value) {
			$value = (is_array($value) || is_object($value)) ? $this->std_class_object_to_array($value) : $value;
			$array[$key] = $value;
		}
		
		return $array;
	}
}
