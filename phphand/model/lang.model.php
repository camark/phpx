<?php
class LangModel extends PHPHand{
	/**
	 *
	 */
	private $_langset='';
	/**
	 * language list
	 */
	private $_data=array();

	private $_tableList=null;
	/**
	 * init
	 *
	 * initialize visitor's language
	 * so that phphand can print out a user view with proper language
	 * check if the user's lang in db `phphand_lang` table
	 * if not use the config option `default_lang` as absolute user language
	 */
	public function init(){
		$_default=$this->config->get('default_lang');
		if($_default){
			if(isset($_SESSION['lang'])) $lang=$_SESSION['lang'];
			else $lang=strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);

			$langarray=explode('-',$lang);
			if(isset($langarray[1])) $langAppend=$langarray[1];
			else $langAppend=$langarray[0];
			
			$this->checkLangTable();
			$all=$this->share->readTable('phphand_lang');
			
			foreach($all as $langset){
				if(($langset['lang'] && strtolower($langset['lang'])==$lang) || (isset($langAppend) && strtolower($langset['lang'])==$langAppend)){
					$rightlang=$langset['lang'];
					break;
				}
			}
			if(isset($rightlang)){
				$this->_langset=$rightlang;
			}else{
				/**
				 * check if the `default_lang` is a right
				 * lang set
				 * if not trigger_error
				 */
				foreach($all as $langset){
					if($langset['lang']==$_default){
						$rightlang=$_default;
						break;
					}
				}
				if(!isset($rightlang)) exit('default_lang not exists in `phphand_lang_set`');
				$this->_langset=$rightlang;
			}
		}
	}
	/**
	 * checkLangTable
	 *
	 * check the phphand_lang AND its related data
	 * if table or data not exists create them
	 *
	 * check the phphand_lang_{langset} table
	 * if this table not exists create it
	 *
	 */
	private function checkLangTable(){
		$data=$this->share->get('phphand_lang_'.$this->_langset);
		if(!is_array($data)){
			/**
			 * check the phphand_lang seed
			 * if not exists
			 * create this seed and build the phphand_lang table in db
			 */
			$phphandLangSeed=$this->share->get('phphand_lang');
			if(!$phphandLangSeed=='active'){
				$this->db->query("CREATE TABLE IF NOT EXISTS `phphand_lang`(id int(10) NOT NULL AUTO_INCREMENT,`lang` varchar(20) NOT NULL,`title` varchar(30) NOT NULL,PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			}
			$lang=$this->db->getOne("SELECT * FROM phphand_lang");
			if(!$lang){
				$this->db->query("INSERT INTO phphand_lang(lang) VALUES('{$this->_langset}')");
			}
			/**
			 * build lang table in db
			 */
			if($this->_langset) $this->db->query("CREATE TABLE IF NOT EXISTS `phphand_lang_".$this->_langset."`(id int(10) NOT NULL AUTO_INCREMENT,`key` varchar(100) NOT NULL,`value` varchar(200) NULL,PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			//$this->share->set('phphand_lang_'.$this->_langset,array());
		}
	}
	
	/**
	 * switchSql
	 *
	 * development of phphand system developers need NOT to
	 * write any code for Multi-Language target in SQL
	 * this function is perticularly for switching a common SQL
	 * into a multi-language SQL
	 * it would find out all tables this sql relates.Then it tests
	 * if any of these tables is a multi-language-data table
	 *  - if so,check if this table contains a column called `phphand_lang_set`
	 *     - if not create this columnmake a share seed for this column so next
	 *       time there is no need to test in db for saving time
	 *    add `Table_Name`.`phphand_lang_set`='$this->_langset' into SQL
	 */
	public function switchSql($sql){
		if(!$this->_langset) return $sql;
		$sql=strtolower($sql);
		$sqlCache=$this->share->get('phphand_sql_'.$this->_langset.'_'.md5($sql));
		if(!$sqlCache || true){
			$sqlCache=$sql;
			if(preg_match('/\sFROM\s(.+?)(\sWHERE\s|\sLIMIT\s|\sORDER\s|$)/is',$sql,$matches)){
				/**
				 * find out all tables the sql relates to
				 */				
				if(is_null($this->_tableList)) $this->initTableSetting();
				$tableString=$matches[1];
				$tableTemps=explode(',',$tableString);
				$tables=array();
				foreach($tableTemps as $table){
					$table=trim($table);
					/** eligal table name */
					if(!$table) return false;
					if(strpos($table," left join")){

						$table=substr($table,0,strpos($table,"left join"));
					}
					if(strpos($table,"(")===0){
						$table=substr($table,1);
					}
					if(strpos($table,"(")===0){
						$table=substr($table,1);
					}
					if(strpos($table,"(")===0){
						$table=substr($table,1);
					}
					if(strstr($table,' as ')){
						$pair=explode(' as ',$table);
						if(sizeof($pair)!=2) return false;
						$originalTable=trim(trim($pair[0]),'`');
						$statementTable=trim(trim($pair[1]),'`');
						$tables[$originalTable]=$statementTable;
					}elseif(strchr($table,' ')){
						$pair=split(' ',$table);
						if(sizeof($pair)!=2) return false;
						$originalTable=trim(trim($pair[0]),'`');
						$statementTable=trim(trim($pair[1]),'`');
						$tables[$originalTable]=$statementTable;
					}else{
						$table=trim(trim($table),'`');
						$originalTable=$table;
						$statementTable=$table;
						$tables[$table]=$table;
					}
					if(isset($this->_tableList[$originalTable])){
						/**
						 * if this table is a multi-language needing table
						 */
						/**
						 * - first:
						 *     make sure that this table has a column `phphand_lang_set`
						 */
						$result=@mysql_query("ALTER TABLE `{$originalTable}` ADD COLUMN `phphand_lang_set` varchar(20) NULL");
						if($result){
							@mysql_query("UPDATE `{$originalTable}` SET phphand_lang_set='{$this->_langset}'");
						}
						/**
						 * - second:
						 *     find out a proper position to insert the append sql part
						 *     if WHERE exists insert it at the end of the WHERE part with an `AND`
						 *     else if FROM exists insert it at the end of FROM part with a `WHERE`
						 */
						
						if(preg_match('/\sWHERE\s(.+?)(\sORDER\s|\sLIMIT\s|$)/is',$sqlCache)){
							$sqlCache=preg_replace('/\sWHERE\s(.+?)(\sORDER\s|\sLIMIT\s|$)/is'," WHERE \\1 AND `$statementTable`.`phphand_lang_set`='{$this->_langset}'\\2",$sqlCache);
						}else{
							$sqlCache=preg_replace('/\sFROM\s(.+?)(\sORDER\s|\sLIMIT\s|$)/is'," FROM \\1 WHERE `$statementTable`.`phphand_lang_set`='{$this->_langset}'\\2",$sqlCache);
						}
					}
				}
			}
			$this->share->set('phphand_sql_'.$this->_langset.'_'.md5($sql),$sqlCache);
		}
		//echo 'sql:'.$sqlCache.'<br/>';
		return $sqlCache;
	}
	
	/**
	 * initTableSetting
	 * 
	 * initialize the language setting for all Tables
	 *
	 */
	function initTableSetting(){
		if(!file_exists($this->env->get('app_dir').'/lang_table.php')){
			copy(PHPHAND_DIR.'/Template/Config/LangDefault.php',$this->env->get('app_dir').'/lang_table.php');
		}
		include $this->env->get('app_dir').'/lang_table.php';
		$this->_tableList=$langTableList;
		unset($langTableList);
		if(isset($externDir)){
			include $externDir.'/lang_table.php';
			$this->_tableList=array_merge(array_change_key_case($langTableList),$this->_tableList);
		}
	}

	/**
	 * readFromFile
	 *
	 * read a set of config info from a php file
	 *
	 * @param filename String
	 */
	function readFromFile($filename){
		try{
			/**
			 * a return array() format file
			 * would return a value
			 */
			include $filename;
		}catch(Exception $e){
			//on error happends do nothing
		}
		return $config;
	}
	
	function get($key){
		if(!$this->_langset) return $key;
		$langList=$this->share->get('phphand_lang_'.$this->_langset);
		if(!is_array($langList) || $this->config->get('debug_mode')){
			$query=$this->db->query("SELECT * FROM `phphand_lang_".$this->_langset."`");
			$langList=array();
			while($rs=$this->db->fetchArray($query)){
				$langList[$rs['key']]=$rs['value'];
			}
			$this->share->set('phphand_lang_'.$this->_langset,$langList);
		}
		if(!isset($langList[$key])){
			$this->env->get('app_dir').
			$lang=$this->db->getOne("SELECT * FROM `phphand_lang_".$this->_langset."` WHERE `key`='$key'");
			if(!$lang){
				$this->db->query("INSERT INTO `phphand_lang_".$this->_langset."`(`key`,`value`) VALUES('$key','$key')",true);
			}
			$this->share->remove('phphand_lang_'.$this->_langset);
			$langList[$key]=$key;
		}
		return $langList[$key];
	}
	
	function getLangset(){
		if($this->_langset=='') return 'cn';
		return $this->_langset;
	}
	
	function set($lang){
		$_SESSION['lang']=$lang;
	}
	
	/**
	 * isLangTable
	 * 
	 * check if a table is a multi-lang required table
	 *
	 * @param $table String
	 */
	function isLangTable($table){
		if(!$this->_tableList){
			$this->initTableSetting();
		}
		if(isset($this->_tableList[$table])) return true;
		return false;
	}
}