<?php
class DsModel extends PHPHand_Model{
	private $tables=array();
	
	function get_config($tablename){
		$config_dir=$this->env->get('app_dir').'/config';
		if(!is_dir($config_dir)){
			mkdir($config_dir);
		}
		$config_file=$config_dir.'/'.$tablename.'.php';
		if(file_exists($config_file)){
			include $config_file;
			return $config;
		}
		
		if(!isset($this->tables[$tablename])){
			$query=$this->db->query('SHOW COLUMNS FROM '.$tablename);
			$table=array();
			while($column=mysql_fetch_array($query)){
				$table[$column['Field']]=$column;
			}
			$this->tables[$tablename]=$table;
		}
		if(!isset($this->tables[$tablename])){
			return false;
		}
		
		$php ="<?php\r\n";
		$php.="\$config=array(\r\n";
		foreach($this->tables[$tablename] as $field => $column){
			$type=preg_replace("/\(.+?\)/","",$column['Type']);
			switch($type){
				case 'varchar':
				case 'char':
					preg_match("/\((.+?)\)/",$column['Type'],$match);
					$max=$match[1];
					$php.= "	'$field' => array(\r\n";
					$php.= "		'showname' => '$field',\r\n";
					$php.= "		'row' => '$field',\r\n";
					$php.= "		'type' => 'string',\r\n";
					$php.= "		'maxlength' => $max,\r\n";
					$php.= "		'minlength' => 0,\r\n";
					$php.= "		'null' => ".($column['Null']=='NO'?'false':'true').",\r\n";
					$php.= "		'input' => 'text',\r\n";
					$php.= "	),\r\n";
					break;
				case 'int':
				case 'tinyint':
					preg_match("/\((.+?)\)/",$column['Type'],$match);
					$max=$match[1];
					$php.= "	'$field' => array(\r\n";
					$php.= "		'showname' => '$field',\r\n";
					$php.= "		'row' => '$field',\r\n";
					$php.= "		'type' => 'int',\r\n";
					$php.= "		'max' => $max,\r\n";
					$php.= "		'min' => 0,\r\n";
					$php.= "		'null' => ".($column['Null']=='NO'?'false':'true').",\r\n";
					if($column['Extra']!='auto_increment'){
						$php.= "		'input' => 'text',\r\n";
					}else{
						$php.= "		'input' => 'none',\r\n";
					}
					$php.= "	),\r\n";
					break;
				case 'text':
				case 'mediumtext':
				case 'longtext':
					$php.= "	'$field' => array(\r\n";
					$php.= "		'showname' => '$field',\r\n";
					$php.= "		'row' => '$field',\r\n";
					$php.= "		'type' => 'int',\r\n";
					$php.= "		'max' => $max,\r\n";
					$php.= "		'min' => 0,\r\n";
					$php.= "		'null' => ".($column['Null']=='NO'?'false':'true').",\r\n";
					$php.= "		'input' => 'textarea',\r\n";
					$php.= "	),\r\n";
					break;
			}
		}
		
		$php.=");\r\n";
		file_put_contents($config_file,$php);
		include $config_file;
		return $config;
	}
	
	function get_input_table($table_config){
		if(is_string($table_config)){
			$table_config=$this->get_config($table_config);
		}
		$table_html='<table class="input_table" width="100%" cellspacing="0">';
		foreach($table_config as $field => $config){
			switch($config['input']){
				case 'textarea':
					$table_html.='<tr>';
					$table_html.='<td>'.$config['showname'].'</td>';
					$table_html.='<td><textarea name="'.$field.'"></textarea></td>';
					$table_html.='</tr>';
					break;
				case 'text':
					$table_html.='<tr>';
					$table_html.='<td>'.$config['showname'].'</td>';
					$table_html.='<td><input type="text" name="'.$field.'" class="input" /></td>';
					$table_html.='</tr>';
					break;
				case 'checkbox':
					break;
				case 'radio':
					break;
			}
		}
		$table_html.='<tr>';
		$table_html.='<td>&nbsp;</td>';
		$table_html.='<td><input type="submit" name="submit2" value="submit" /></td>';
		$table_html.='</tr>';
		$table_html.='</table>';
		return $table_html;
	}
}
