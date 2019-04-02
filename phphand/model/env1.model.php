<?php
class EnvModel extends PHPHand{
	public $data=array();
	private $inited=false;

	function init($appname='App'){
		$this->inited=true;
		/** define WebRoot Url*/
		if(substr(php_sapi_name(),0,3)=='cgi'){
			/** in cgi/fcig mode */
			$temp  = explode('.php',$_SERVER["PHP_SELF"]);
			$phpFile= rtrim(str_replace($_SERVER["HTTP_HOST"],'',$temp[0].'.php'),'/');
		}else{
			$phpFile= rtrim($_SERVER["SCRIPT_NAME"],'/');
		}
		$this->set('php_file',$phpFile);
		
		$root = dirname($phpFile);
		$url = ($root=='/' || $root=='\\')?'':$root;
		
		
		$app_url = $_SERVER['PHP_SELF'];
		$app_url = str_replace('\\','/',dirname($app_url));
		if(strpos($app_url,'.php')>0)
		{
			$app_url = dirname(preg_replace('/\.php.+?$/is','',$app_url));
		}
		$this->set('app_url',$app_url);
		$this->set('php',preg_replace('/\.php.*?$/is','',$_SERVER['PHP_SELF']).'.php');
		

		$this->set('phphand_dir',PHPHAND_DIR);
		
		$script = $_SERVER['SCRIPT_FILENAME'];
		
		if(strpos($_SERVER['SERVER_SOFTWARE'],"Win")>0)
		{
			//Windows
			if(empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
				$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
			}
			if(empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['PATH_TRANSLATED'])) {
				$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
			}
			$str = str_replace('\\','/',strtolower(PHPHAND_DIR));
			$find = str_replace('\\','/',strtolower($_SERVER['DOCUMENT_ROOT']));
			$phphand_url = str_replace($find,'',$str);
		}else{
			$_SERVER['DOCUMENT_ROOT'] = str_replace($_SERVER['PHP_SELF'],'',$_SERVER['SCRIPT_FILENAME']);
			$phphand_url = str_replace(str_replace('\\,','/',$_SERVER['DOCUMENT_ROOT']),'',str_replace('\\','/',PHPHAND_DIR));
		}
		
		
		
		define('__ROOT__',str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])));

		
		$this->set('phphand_url',$phphand_url);
		//echo dirname($_SERVER['SCRIPT_FILENAME']).'<br />';
		//echo $_SERVER['DOCUMENT_ROOT'].'<br />';
		
		/** Root Dir */
		$this->set('root_dir',__ROOT__);
		$this->set('app',$appname);
		$appDir=str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])).'/'.__APP__;
		$this->set('app_dir',$appDir);
		if(!is_dir($appDir)){
			//include PHPHAND_DIR.'/model/dir_helper.model.php';
			//PHPHand_Dir::buildBase($appDir);
			//PHPHand_Dir::makeFile($this);
		}
		
		/** Data dir */
		if($this->config->get('data_dir')){
			$this->set('data_dir',$this->config->get('data_dir'));
		}else{
			$this->set('data_dir',$this->get('app_dir').'/wallet');
		}
		if(!is_dir($this->get('data_dir'))) mkdir($this->get('data_dir'));
		
		$this->set('clock_start',microtime());
		$this->set('now',time());
		
		/** Action Class */
		$class=$this->query->get('class');
		$class=strtoupper($class[0]).substr($class,1);
		$this->set('class',$class);
		
		/** Action method */
		$method=$this->query->get('method');
		$this->set('method',$method);
		
		/** PAC action */
		$pac=$this->query->get('pac');
		$this->set('pac',$pac);
		
		/** debug mode */
		$this->set('debug_mode',$this->config->get('debug_mode'));
		
	}
	
	function set($name,$value){
		if(!$this->inited) $this->init(self::$app_name);
		$this->data[strtolower($name)]=$value;
	}
	
	function get($name,$strtolower=false){
		if(!$this->inited) $this->init(self::$app_name);
		if($strtolower) return strtolower($this->data[strtolower($name)]);
		return $this->data[strtolower($name)];
	}
}