<?php
/**
 * PHPHand Framework
 *
 * This php script file runs on PHP 5.x enviroment
 * and is part of the PHPHand Framework.
 * PHPHand Framework is an open source web developing framework
 * for any purpose.
 * You can use it freely but we really thank you for
 * keeping this authorization note.
 *
 * PHPHand Version 2.0
 * Website Address: http://www.phphand.cn
 */

include_once PHPHAND_DIR.'/Model/Form/FormSecurity.php';
set_magic_quotes_runtime(0);
class Form1Model extends PHPHand_Model
{
	
	static $securityKey='';
	static $securityCode='';
	private $insertId=0;
	private $term=array();
	private $state=array();
	private $elements=array();
	private $files=array();
	private $uploaded=array();
	
	
	function check($name='')
	{
		if($name=='') $name='CheckCode';	
		if(strtoupper($_POST[$name])==$_SESSION['PHPHAND_CheckCode'])
		{
			$_SESSION['PHPHAND_CheckCode']='';
			return true;
		}
		else
		{
			return false;
		}
	}
		
	function setSecurity($key,$code){
		self::$securityKey=$key;
		self::$securityCode=$code;
	}
	
	function addElement($name,$append='string'){
		if(is_string($append)){
			$append=array('type' => $append);
		}
		$this->elements[$name]=$append;
	}
	
	function addFile($name,$append=array()){
		$this->files[$name]=$append;
	}
	
	function addTerm($name,$value=NULL,$append='type{string}'){
		if(!preg_match("/(^|\})type\{/is",$append)) $append.="type{string}";
		if(is_null($value)){
			$this->term['column{'.$name.'}'.$append]=$_POST[$name];
		}else{
			$this->term['column{'.$name.'}'.$append]=$value;
		}
	}
	
	function addState($term,$value){
		$this->state[$term]=$value;
	}
	
	function insert($table,$print=false)
	{
		if($this->securityKey){
			if(!FormSecurity::check(self::$securityKey,self::$securityCode)){
				return $this->lang->get('form security checking failed');
			}
		}
		if(strpos($table,'phphand_')!==0) $table=PHPHand_Config::getInstance()->get('db_pre').$table;
		$columns="";
		$values="";
		$checker="";
		
		$columnArray=array();
		foreach($this->term as $name => $value)
		{
			$name=preg_replace("/^(string|int|password|now|date|session|random|lang)_(.+?)$/is","type{\\1}column{\\2}",$name);
			preg_match_all("/([a-zA-Z]+)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key => $option){
				$setitem=$matches[2][$key];
				$defination[$option]=trim($setitem);
			}
			if(isset($defination['column']) && $defination['column']){
				$column=$defination['column'];
				
				if(isset($this->state[$column])){
					#从state添加检查条件
					preg_match_all("/([a-zA-Z]+)\{([\s\S]*?)\}/is",$this->state[$column],$matches,PREG_PATTERN_ORDER);
					foreach($matches[1] as $key => $option){
						$set=$matches[2][$key];
						$defination[$option]=trim($set);
					}
				}
				$columnArray[$defination['column']]=array('check'=>$defination,'value'=>$value);
			}
			unset($defination);
		}
		foreach($this->elements as $name => $check){
			if(!isset($check['showname'])) $check['showname']='字段'.$name;
			$columnArray[$name]=array('check' => $check,'value' => get_magic_quotes_gpc()?$_POST[$name]:addslashes($_POST[$name]));
		}
		
		foreach($_POST as $name => $value){
			$name=preg_replace("/^(string|int|password|now|date|session|random|lang|check)_(.+?)$/is","type{\\1}column{\\2}",$name);
			preg_match_all("/([a-zA-Z]+)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key => $option){
				$setitem=$matches[2][$key];
				$defination[$option]=trim($setitem);
			}
			if(isset($defination['column']) && $defination['column']){
				$column=$defination['column'];
				$columnArray[$column]=array('check'=>$defination,'value'=>$value);
			}
			unset($defination);
		}
		
		foreach($columnArray as $column => $v){
			$value=$v['value'];
			$defination=$v['check'];

			#表单有效性检查
			if(isset($defination['minlength'])){
				$minlength=intval($defination['minlength']);
				if(!$minlength) $minlength=0;
				if(mb_strlen($value,'utf-8')<$minlength){
					if(isset($defination['showname'])){
						return array(false,$defination['showname'].'长度过短');
					}else{
						return array(false,'项目`'.$column.'`长度过短');
					}
				}
			}
			if(isset($defination['maxlength'])){
				$maxlength=intval($defination['maxlength']);
				if(!$maxlength) $maxlength=0;
				if(mb_strlen($value,'utf-8')>$maxlength){
					if(isset($defination['showname'])){
						return array(false,$defination['showname'].'长度过长');
					}else{
						return array(false,'项目`'.$column.'`长度过长');
					}
				}
			}
			
			if(is_array($value)) $value=implode(",",$value);
			switch($defination['condition']){
				case 'notsame':
					if($checker) $checker.=" AND ";
					$checker.="`$column`='$value'";
					break;
			}

			switch($defination['type']){
				case "string":
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.="'".str_replace("'","'",$value)."'";
					break;
				case "int":
					if(!preg_match("/^[0-9]+?$/i",$value)) return array(false,$defination['showname'].'必须是一个数字');
					$value=(int)$value;
					if(isset($defination['min']) && $value<$defination['min']) return array(false,$defination['showname'].'不能小于'.$defination['min']);
					if(isset($defination['max']) && $value>$defination['max']) return array(false,$defination['showname'].'不能大于'.$defination['max']);
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.=$value;
					break;
				case "password":
					if(isset($_POST['repeat_'.$column]) && $_POST['repeat_'.$column]!=$value){
						return array(false,'两次输入的密码不一致');
					}
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.="'".md5($value)."'";
					break;
				case "now":
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.=time();
					break;
				case "date":
					if(!preg_match("/^[0-9]+?\-[0-9]+?\-[0-9]+?$/is",$value)) return array(false,'日期不正确');
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.="unix_timestamp('".$value."')";
					break;
				case "session":
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.="'".$_SESSION[$value]."'";
					break;
				case "random":
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$min=isset($defination['min'])?intval($defination['min']):1;
					$max=isset($defination['max'])?intval($defination['max']):99;
					$value.="'".time()."_".rand($min,$max)."'";
					break;
				case 'lang':
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.="'".$this->lang->getLangset()."'";
					break;
				case 'order':
					if(!$value) $value='1=1';
					$rs=$this->db->getOne("SELECT `$column` FROM `$table` WHERE $value ORDER BY `$column` DESC LIMIT 1");
					if(!$rs) $order=1;
					else $order=$rs[$column]+1;
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.="'$order'";
					break;
			}
			unset($defination);
		}
		if($checker){
			$sql="SELECT * FROM `$table` WHERE $checker";
			$rs=$this->db->getOne($sql);
			if($rs) return array(false,'不能创建一条重复的记录');;
		}
		
		foreach($_FILES as $name => $value){
			$name=preg_replace("/^(string|int|password|now|date|session|random|lang|check)_(.+?)$/is","type{\\1}column{\\2}",$name);
			preg_match_all("/([a-zA-Z_]+)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key => $option){
				$setitem=$matches[2][$key];
				$defination[$option]=trim($setitem);
			}
			
			if(isset($defination['column']) && $defination['column']){
				$column=$defination['column'];
				$defination['yname']=$name;
				$this->files[$column]=$defination;//array('check'=>$defination,'value'=>$value);
			}
			unset($defination);
		}
		
		foreach($this->files as $name => $defination){
			preg_match_all("/(type|maxsize|null|path|column|ext_column)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			
			
			if(!isset($defination['type'])){
				$defination['type']=array('ALL');
			}else{
				$defination['type']=explode(',',$defination['type']);
			}

			if(!isset($defination['maxsize'])) $defination['maxsize']=1024*1024*10;
			if(!isset($defination['null'])) $defination['null']=false;
			if(!isset($defination['showname'])) $defination['showname']='文件字段'.$name;
			if(!isset($defination['path'])){
				$defination['path']='.';
			}
			$defination['path']=str_replace('__','..',$defination['path']);
			if(isset($defination['date']) && $defination['date']==true){
				$date=date('/y/m/d',time());
			}else{
				$date='';
			}
				//$defination['path']=preg_replace("/\{(.+?)\}/ise","date('\\1',time())",$defination['path']);
			//保证目录完整性
			$tempPathArray=split('/',$defination['path'].$date);
			$tempPath='';
			foreach($tempPathArray as $f){
				if($tempPath=='') $tempPath=$f;
				else $tempPath.='/'.$f;
				if(!is_dir($tempPath)) @mkdir($tempPath);
			}

			//Check if file is null
			//var_dump($_FILES);
			if(isset($defination['yname'])){
				$file=$_FILES[$defination['yname']];
			}else{
				$file=$_FILES[$name];
			}
			if($file['error'] && $defination['null']==false){
				return array(false,"请为{$defination['showname']}选择文件");
			}
			if($file['size']==0 && $defination['null']==false){
				return array(false,"请为{$defination['showname']}上传文件");
			}
			if(!$file['error'] && $file['size']>0){
				//Check max file size
				if((int)$file['size']>$defination['maxsize']){
					return array(false,"{$defination['showname']}文件太大了");
				}
				//Check file type
				$dotPosition=strrpos($file['name'], ".");
				if(!$dotPosition){
					return array(false,"{$defination['showname']}文件类型缺乏");
				}
				$ext=substr($file['name'],$dotPosition+1,strlen($file['name'])-$dotPosition);
				if(!in_array(strtolower($ext),$defination['type']) && !in_array('ALL',$defination['type'])){
					return array(false,"{$defination['showname']}文件类型不正确。");
				}
				//Add file into SQL
				$fileTempName=time()."_".rand(1000,9999).".".$ext;
				if($columns!=""){
					$columns.=",";
					$values.=",";
				}
				$columns.="`$name`";
				$values.="'".$date.'/'.$fileTempName."'";
				
				if($defination['ext_column']){
					$columns.=",`{$defination['ext_column']}`";
					$values.=",'$ext'";
				}
				@move_uploaded_file($file['tmp_name'],$this->env->get('root_dir').'/'.$defination['path'].$date.'/'.$fileTempName);
				$this->uploaded[]=array('temp' =>$date.'/'.$fileTempName,'ext' => $ext);
			}
		}
		
		/*
		 foreach($this->term as $name => $value){
			if($columns!=""){
				$columns.=",";
				$values.=",";
			}
			$columns.='`'.$name.'`';
			$values.="'".$value."'";
		}*/
		
		$sql="INSERT INTO `$table`($columns) VALUES($values)";
		$this->db->query($sql);
		$insertId = $this->db->insertId();
		if($insertId==0) return array(false,"插入数据库失败".($print?(':'.$sql):''));
		return $insertId;
	}
	
	function update($table,$print=false)
	{
		if(strpos($table,'phphand_')!==0) $table=$this->config->getInstance()->get('db_pre').$table;
		$set="";
		$condition="";

		
		$columnArray=array();
		foreach($this->term as $name => $value)
		{
			$name=preg_replace("/^(string|int|password|now|date|session|random|lang)_(.+?)$/is","type{\\1}column{\\2}",$name);
			preg_match_all("/([a-zA-Z]+)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key => $option){
				$setitem=$matches[2][$key];
				$defination[$option]=trim($setitem);
			}
			if(isset($defination['column']) && $defination['column']){
				$column=$defination['column'];
				
				if(isset($this->state[$column])){
					#从state添加检查条件
					preg_match_all("/([a-zA-Z]+)\{([\s\S]*?)\}/is",$this->state[$column],$matches,PREG_PATTERN_ORDER);
					foreach($matches[1] as $key => $option){
						$set=$matches[2][$key];
						$defination[$option]=trim($set);
					}
				}
				$columnArray[$defination['column']]=array('check'=>$defination,'value'=>$value);
			}
			unset($defination);
		}
		foreach($this->elements as $name => $check){
			if(!isset($check['showname'])) $check['showname']='字段'.$name;
			$columnArray[$name]=array('check' => $check,'value' => get_magic_quotes_gpc()?$_POST[$name]:addslashes($_POST[$name]));
		}
		
		foreach($_POST as $name => $value){
			$name=preg_replace("/^(string|int|password|now|date|session|random|lang|check)_(.+?)$/is","type{\\1}column{\\2}",$name);
			preg_match_all("/([a-zA-Z]+)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key => $option){
				$setitem=$matches[2][$key];
				$defination[$option]=trim($setitem);
			}
			if(isset($defination['column']) && $defination['column']){
				$column=$defination['column'];
				$columnArray[$column]=array('check'=>$defination,'value'=>$value);
			}
			unset($defination);
		}
		
		foreach($columnArray as $column => $v)
		{
			$value=$v['value'];
			$defination=$v['check'];
			
			if(isset($defination['minlength'])){
				$minlength=intval($defination['minlength']);
				if(!$minlength) $minlength=0;
				if(mb_strlen($value,'utf-8')<$minlength){
					if(isset($defination['showname'])){
						return array(false,$defination['showname'].'长度过短');
					}else{
						return array(false,'项目`'.$column.'`长度过短');
					}
				}
			}
			if(isset($defination['maxlength'])){
				$maxlength=intval($defination['maxlength']);
				if(!$maxlength) $maxlength=0;
				if(mb_strlen($value,'utf-8')>$maxlength){
					if(isset($defination['showname'])){
						return array(false,$defination['showname'].'长度过长');
					}else{
						return array(false,'项目`'.$column.'`长度过长');
					}
				}
			}
			if(is_array($value)) $value=implode(",",$value);
			switch($defination['type']){
				case 'string':
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`='".str_replace("'","''",$value)."'";
					break;
				case 'int':
					if(!preg_match("/^[0-9]+?$/i",$value)) return array(false,$defination['showname'].'必须是一个数字');
					$value=(int)$value;
					if(isset($defination['min']) && $value<$defination['min']) return array(false,$defination['showname'].'不能小于'.$defination['min']);
					if(isset($defination['max']) && $value>$defination['max']) return array(false,$defination['showname'].'不能大于'.$defination['max']);
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`=".$value;
					break;
				case 'password':
					if($value!=''){
						if($set!=''){
							$set.=',';
						}
						$set.="`$column`='".md5($value)."'";
					}
					break;
				case 'now':
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`='".time()."'";
					break;
				case "date":
					if(!preg_match("/^[0-9]+?\-[0-9]+?\-[0-9]+?$/is",$value)) return array(false,'日期不正确');
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`=unix_timestamp('".$value."')";
					break;
				case 'session':
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`='".$_SESSION[$value]."'";
					break;
				case 'random':
					if($set!=''){
						$set.=',';
					}
					$min=isset($defination['min'])?intval($defination['min']):1;
					$max=isset($defination['max'])?intval($defination['max']):99;
					$set.="`$column`='".time()."_".rand($min,$max)."'";
					break;
				case 'lang':
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`='".$this->lang->getLangset()."'";
					break;
				case 'check':
					if($condition!=''){
						$condition.=' and ';
					}
					$condition.="`$column`='".str_replace("''","'",$value)."'";
					break;
			}
			unset($defination);
		}
		
		if($condition=='') return array(false,"更新条件缺乏");
		/**
		 * @format:
		 * @ type{jpg,gif}maxsize{1000}null{false}_column
		 */
		foreach($_FILES as $name => $value){
			$name=preg_replace("/^(string|int|password|now|date|session|random|lang|check)_(.+?)$/is","type{\\1}column{\\2}",$name);
			preg_match_all("/([a-zA-Z_]+)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			foreach($matches[1] as $key => $option){
				$setitem=$matches[2][$key];
				$defination[$option]=trim($setitem);
			}
			$defination['yname']=$name;
			if(isset($defination['column']) && $defination['column']){
				$column=$defination['column'];
				$this->files[$column]=$defination;//array('check'=>$defination,'value'=>$value);
			}
			unset($defination);
		}

		foreach($this->files as $name => $defination){
			preg_match_all("/(type|maxsize|null|path|column|ext_column)\{([\s\S]*?)\}/is",$name,$matches,PREG_PATTERN_ORDER);
			if(!isset($defination['type'])){
				$defination['type']=array('ALL');
			}else{
				$defination['type']=explode(',',$defination['type']);
			}
			if(!isset($defination['maxsize'])) $defination['maxsize']=1024*1024*10;
			if(!isset($defination['null'])) $defination['null']=true;
			if(!isset($defination['showname'])) $defination['showname']='文件字段'.$name;
			if(!isset($defination['path'])){
				$defination['path']='.';
			}
			$defination['path']=str_replace('__','..',$defination['path']);
			if(isset($defination['date']) && $defination['date']==true){
				$date=date('/y/m/d',time());
			}else{
				$date='';
			}
				//$defination['path']=preg_replace("/\{(.+?)\}/ise","date('\\1',time())",$defination['path']);
			//保证目录完整性
			$tempPathArray=split('/',$defination['path'].$date);
			$tempPath='';
			foreach($tempPathArray as $f){
				if($tempPath=='') $tempPath=$f;
				else $tempPath.='/'.$f;
				if(!is_dir($tempPath)) @mkdir($tempPath);
			}
			
			//Check if file is null
			//var_dump($_FILES);
			if(isset($defination['yname'])){
				$file=$_FILES[$defination['yname']];
			}else{
				$file=$_FILES[$name];
			}
			if($file['error'] && $defination['null']==false){
				return array(false,"请为{$defination[showname]}选择文件");
			}
			if($file['size']==0 && $defination['null']==false){
				return array(false,"请为{$defination[showname]}上传文件");
			}
			if(!$file['error'] && $file['size']>0){
				//Check max file size
				if((int)$file['size']>$defination['maxsize']){
					return array(false,"{$defination[showname]}文件太大了");
				}
				//Check file type
				$dotPosition=strrpos($file['name'], ".");
				if(!$dotPosition){
					return array(false,"{$defination[showname]}文件类型缺乏");
				}
				$ext=substr($file['name'],$dotPosition+1,strlen($file['name'])-$dotPosition);
				if(!in_array(strtolower($ext),$defination['type']) && !in_array('ALL',$defination['type'])){
					return array(false,"{$defination[showname]}文件类型不正确。");
				}
				//Add file into SQL
				$rs=$this->db->getOne("SELECT `$name` FROM `$table` WHERE $condition");
				if(!$rs) return array(false,'和上传文件相关的记录不存在');
				unlink($this->env->get('root_dir').'/'.$defination['path'].'/'.$rs[$name]);
				$fileTempName=time()."_".rand(1000,9999).".".$ext;
				if($set!=''){
					$set.=',';
				}
				$set.="`$name`='".$date.'/'.$fileTempName."'";
				$fileTempName=$date.'/'.$fileTempName;
				
				if($defination['ext_column']){
					$set.=",`{$defination['ext_column']}`='$ext'";
				}
				
				move_uploaded_file($file['tmp_name'],$this->env->get('root_dir').'/'.$defination['path'].$fileTempName);
				$this->uploaded[]=array('temp' => $fileTempName,'ext' => $ext);
			}
		}
		if($set=='') return array(false,'缺少更新项目');
		if($condition=='') return array(false,'缺少更新条件');
		if($print) echo("update $table set $set where $condition");
		$this->db->query("update $table set $set where $condition");
		return true;
	}
	
	function getJavascript(){
		$script="<script language=\"javascript\"><!--\r\n";
		$script.="function PHPHandCheckForm(f){";
		foreach($this->elements as $name => $append){
			$showname=isset($append['showname'])?$append['showname']:'字段';
			if(isset($append['minlength'])){
				$script.="if(f.elements.namedItem('$name').value.length<".$append['minlength']."){alert('{$showname}长度过短');f.elements.namedItem('$name').focus();return false;}";
			}
			switch($append['type']){
				case 'email':
					$script.="var re = /^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;if(!re.test(f.elements.namedItem('$name').value)){alert('EMAIL格式不正确');f.elements.namedItem('$name').focus();return false;}";
					break;
				case 'string':
					break;
				case 'int':
					$script.="if(isNaN(f.elements.namedItem('$name').value)){alert('{$showname}整数格式错误');f.elements.namedItem('$name').focus();return false;}";
					break;
				default:
			}
		}
		$script.="}";
		$script.="\r\n//--></script>";
		return $script;
	}
	
	function getUploadedFiles(){
		return $this->uploaded;
	}
	
	function reset(){
		$insertId=0;
		$this->term=array();
		$this->state=array();
		$this->elements=array();
		$this->files=array();
		$this->uploaded=array();
	}
}
?>