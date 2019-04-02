 <?php
/**
 * PHPHand开发框架
 *
 * 表单处理模型
 *
 * PHPHand Version 2.0
 * Website Address: http://www.phphand.com
 */

set_magic_quotes_runtime(0);
class FormModel extends PHPHand_Model
{
	
	static $securityKey='';
	static $securityCode='';
	private $insertId=0;
	private $term=array();
	private $state=array();
	private $elements=array();
	private $files=array();
	private $uploaded=array();
	private $db_pre_null=false;
	private $_check_mode=false;
	
	
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
	
	function checkMode($mode=NULL){
		if(!is_null($mode)){
			$this->_check_mode=$mode;
		}else{
			return $this->_check_mode;
		}
	}
	
	function set_db_pre_null(){
		$this->db_pre_null=true;
		return $this;
	}
		
	function setSecurity($key,$code){
		self::$securityKey=$key;
		self::$securityCode=$code;
	}
	
	/**
	 * init函数
	 *
	 * 自动根据用户的表单配置文件生成表单的验证
	 */
	function init($setup){
		foreach($setup as $name => $append){
			if(!is_array($append)){
				$append=array(
					'type' => 'set',
					'value' => $append,
				);
			}
			switch($append['type']){
				case 'file':
					$append['type']=$append['ext'];
					$this->addFile($name,$append);
					break;
				case 'check':
					$this->addTerm($name,$append['value'],'type{check}');
					break;
				case 'set':
					$this->addTerm($name,$append['value']);
					break;
				default:
					$this->addElement($name,$append);
					break;
			}
		}
		return $this;
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
		if(!$this->db_pre_null){
			if(strpos($table,'phphand_')!==0) $table=$this->config->get('db_pre').$table;
		}
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
			
			if(isset($defination['null']) && $defination['null'] && !$value){
				continue;
			}

			#表单有效性检查
			if(isset($defination['null']) && !$defination['null']){
				if($value==''){
					$this->action->error($defination['showname'].'不能为空',$column);
				}
			}
			
			if(!$defination['null'] && isset($defination['minlength'])){
				$minlength=intval($defination['minlength']);
				if(!$minlength) $minlength=0;
				if(mb_strlen($value,'utf-8')<$minlength){
					$this->action->error($defination['showname'].'长度过短',$column);
				}
			}
			if(isset($defination['maxlength'])){
				$maxlength=intval($defination['maxlength']);
				if(!$maxlength) $maxlength=0;
				if(mb_strlen($value,'utf-8')>$maxlength){
					$this->action->error($defination['showname'].'长度过长',$column);
				}
			}
			if(isset($defination['pattern'])){
				if(!preg_match("/".$defination['pattern']."/is",$value)){
					$this->action->error($defination['showname'].'格式不正确',$column);
				}
			}
			
			if(is_array($value)) $value=implode(",",$value);
			switch(@$defination['condition']){
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
					$value=(int)$value;
					if(isset($defination['min']) && $value<$defination['min']) $this->action->error($defination['showname'].'不能小于'.$defination['min'],$column);
					if(isset($defination['max']) && $value>$defination['max']) $this->action->error($defination['showname'].'不能大于'.$defination['max'],$column);
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.=$value;
					break;
				case "float":
					$value=(float)$value;
					if(isset($defination['min']) && $value<$defination['min']) $this->action->error($defination['showname'].'不能小于'.$defination['min'],$column);
					if(isset($defination['max']) && $value>$defination['max']) $this->action->error($defination['showname'].'不能大于'.$defination['max'],$column);
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.=$value;
					break;
				case "password":
					if(isset($_POST['repeat_'.$column]) && $_POST['repeat_'.$column]!=$value){
						$this->action->error('两次输入的密码不一致','repeat_'.$column);
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
					if(!preg_match("/^[0-9]+?\-[0-9]+?\-[0-9]+?$/is",$value)) $this->action->error('日期不正确',$column);
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
					$values.="'".$_SESSION[$column]."'";
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
				case 'array':
					$value=implode(',',$_POST[$column]);
					if($columns!=""){
						$columns.=",";
						$values.=",";
					}
					$columns.='`'.$column.'`';
					$values.="'".$value."'";
					break;
			}
			unset($defination);
		}
		if($checker){
			$sql="SELECT * FROM `$table` WHERE $checker";
			$rs=$this->db->getOne($sql);
			if($rs) $this->action->message('不能创建一条重复的记录');;
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
				$this->action->error("请为{$defination['showname']}选择文件",$column);
			}
			if($file['size']==0 && $defination['null']==false){
				$this->action->error("请为{$defination['showname']}上传文件",$column);
			}
			if(!$file['error'] && $file['size']>0){
				//Check max file size
				if((int)$file['size']>$defination['maxsize']){
					$this->action->error("{$defination['showname']}文件太大了",$column);
				}
				//Check file type
				$dotPosition=strrpos($file['name'], ".");
				if(!$dotPosition){
					$this->action->error("{$defination['showname']}文件类型缺乏",$column);
				}
				$ext=substr($file['name'],$dotPosition+1,strlen($file['name'])-$dotPosition);
				if(!in_array(strtolower($ext),$defination['type']) && !in_array('ALL',$defination['type'])){
					$this->action->error("{$defination['showname']}文件类型不正确。",$column);
				}
				//Add file into SQL
				$fileTempName=time()."_".rand(1000,9999).".".$ext;
				if($columns!=""){
					$columns.=",";
					$values.=",";
				}
				$columns.="`$name`";
				$values.="'".$date.'/'.$fileTempName."'";

				if(@$defination['ext_column']){
					$columns.=",`{$defination['ext_column']}`";
					$values.=",'$ext'";
				}
				@move_uploaded_file($file['tmp_name'],$this->env->get('root_dir').'/'.$defination['path'].$date.'/'.$fileTempName);
				$this->uploaded[]=array('temp' =>$date.'/'.$fileTempName,'ext' => $ext);
			}
		}
		
		if($this->checkMode()){
			return true;
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
		$result=$this->db->query($sql);
		if(!$result) $this->action->error("插入表{".$table."}失败:".mysql_error() );
		$insertId = $this->db->insertId();
		return $insertId;
	}
	
	function update($table,$print=false)
	{
		if(!$this->db_pre_null){
			if(strpos($table,'phphand_')!==0) $table=$this->config->get('db_pre').$table;
		}
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
			if(isset($defination['null']) && $defination['null'] && !$value){
				continue;
			}

			if(isset($defination['minlength'])){
				$minlength=intval($defination['minlength']);
				if(!$minlength) $minlength=0;
				if(mb_strlen($value,'utf-8')!=0 || $defination['type']!='password'){
					if(mb_strlen($value,'utf-8')<$minlength){
						$this->action->error($defination['showname'].'长度过短',$column);
					}
				}
			}
			if(isset($defination['maxlength'])){
				$maxlength=intval($defination['maxlength']);
				if(!$maxlength) $maxlength=0;
				if(mb_strlen($value,'utf-8')>$maxlength){
					$this->action->error($defination['showname'].'长度过长',$column);
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
					if(!preg_match("/^[0-9]+?$/i",$value)) $this->action->error($defination['showname'].'必须是一个数字',$column);
					$value=(int)$value;
					if(isset($defination['min']) && $value<$defination['min']) $this->action->error($defination['showname'].'不能小于'.$defination['min'],$column);
					if(isset($defination['max']) && $value>$defination['max']) $this->action->error($defination['showname'].'不能大于'.$defination['max'],$column);
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`=".$value;
					break;
				case 'float':
					$value=(float)$value;
					if(isset($defination['min']) && $value<$defination['min']) $this->action->error($defination['showname'].'不能小于'.$defination['min'],$column);
					if(isset($defination['max']) && $value>$defination['max']) $this->action->error($defination['showname'].'不能大于'.$defination['max'],$column);
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`=".$value;
					break;
				case 'password':
					if(isset($_POST['repeat_'.$column]) && $_POST['repeat_'.$column]!=$value){
						$this->action->error('两次输入的密码不一致',$column);
					}
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
					if(!preg_match("/^[0-9]+?\-[0-9]+?\-[0-9]+?$/is",$value)) $this->action->error('日期不正确',$column);
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`=unix_timestamp('".$value."')";
					break;
				case 'session':
					if($set!=''){
						$set.=',';
					}
					$set.="`$column`='".$_SESSION[$column]."'";
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
				case 'array':
					if($set!=''){
						$set.=',';
					}
					$value=implode(',',$_POST[$column]);
					$set.="`$column`='".str_replace("'","''",$value)."'";
					break;
			}
			unset($defination);
		}
		if($condition=='') $this->action->error("更新条件缺乏");
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
				$this->action->error("请为{$defination[showname]}选择文件",$column);
			}
			if($file['size']==0 && $defination['null']==false){
				$this->action->error("请为{$defination[showname]}上传文件",$column);
			}
			if(!$file['error'] && $file['size']>0){
				//Check max file size
				if((int)$file['size']>$defination['maxsize']){
					$this->action->error("{$defination[showname]}文件太大了",$column);
				}
				//Check file type
				$dotPosition=strrpos($file['name'], ".");
				if(!$dotPosition){
					$this->action->error("{$defination[showname]}文件类型缺乏",$column);
				}
				$ext=substr($file['name'],$dotPosition+1,strlen($file['name'])-$dotPosition);
				if(!in_array(strtolower($ext),$defination['type']) && !in_array('ALL',$defination['type'])){
					$this->action->error("{$defination[showname]}文件类型不正确。");
				}
				//Add file into SQL
				$rs=$this->db->getOne("SELECT `$name` FROM `$table` WHERE $condition");
				if(!$rs) $this->action->message('和上传文件相关的记录不存在');
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
		if($set=='') $this->action->error('缺少更新项目');
		if($condition=='') $this->action->error('缺少更新条件');

		if($print) $this->action->error ("update $table set $set where $condition");
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
		$this->_check_mode=false;
	}
}
?>