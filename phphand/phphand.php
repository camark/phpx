<?php
include dirname(__FILE__).'/compatible7.php';
define('PHPHAND_DIR',dirname(__FILE__));
define('PHPHAND_CLOCK_BEGIN',microtime(true));
ini_set('display_errors','On');
error_reporting(E_ALL);
@session_start();

set_error_handler(array('PHPHand','error_handler'));


//包含框架基础文件
include PHPHAND_DIR.'/core/action/phphand_action.php';
include PHPHAND_DIR.'/core/view/phphand_view.php';
if(!class_exists('PHPHand_Model')) include PHPHAND_DIR.'/core/model/phphand_model.php';
/*
include PHPHAND_DIR.'/core/model/phphand_cache.php';
include PHPHAND_DIR.'/core/db/phphand_mysql.php';
include PHPHAND_DIR.'/core/env/phphand_query.php';
include PHPHAND_DIR.'/core/env/phphand_config.php';
include PHPHAND_DIR.'/core/env/phphand_env.php';
include PHPHAND_DIR.'/core/pac/pac.php';*/


/**
 * PHPHand核心类
 * 通过入口文件中本类的PHPHand::getAction方法，可以实现一个PHPHand的应用
 * 所谓入口文件，通常就是网站根目录下的index.php
 */
class PHPHand{
	//控制器钩子
	public static $_action=null;
			  
	protected static $_set=array();
	
	//已加载的模型
	public static $_model=array();
	
	//应用名
	public static $app_name='';
	
	public
	      $view  = null;
		  /*
		  $db    = null,
		  $lang  = null,
		  $cache = null,
		  $query = null,
		  $env   = null,
		  $config= null,
		  $pac   = null,
		  $share = null;*/
	public static $_intoHTML=false;
	
	/**
	 * private __construct
	 * so that PHPHand class cannt form instance
	 */
	function __construct(){
		//$this->query  = PHPHand_Query::getInstance();
		//$this->env    = PHPHand_Env::getInstance();
		//$this->config = PHPHand_Config::getInstance();
		//$this->cache  = PHPHand_Cache::getInstance();
		//$this->db     = PHPHand_Mysql::getInstance();
		$this->view   = PHPHand_View::getInstance();
		//$this->share   = PHPHand_Share::getInstance();
		//$this->pac   = PAC::getInstance();
	}
	
	/**
	 * return instance of PHPHand_Action
	 */
	static function getAction($app='app',$class=null,$method=null){
		//setApplication($app);
		define('__APP__',$app);
		self::$app_name=$app;
		/*
		PHPHand_Config::getInstance() -> init($app);
		PHPHand_Query::getInstance() -> init($app);
		PHPHand_Env::getInstance()-> init($app);*/
		if(!self::$_action){
			$env = @self::load('env');
			/**
			 * initialize the action
			 */
			if($class) $class=strtoupper($class[0]).substr($class,1);
			if(!$class){
				$class=$env->get('class');
			}else{
				$env->set('class',$class);
			}
			$class=strtoupper($class[0]).substr($class,1);
			$class=urldecode($class);
			if(strpos($class,'{')===0)
			{
				$class=trim(trim($class,'}'),'{');
				list($taglib,$tag)=explode(':',$class);
				$dir = @self::load('routine')->get_tag_dir($taglib,$tag);
				if(!$dir) exit('Tag server does not exists');
				$actionFilePath = $dir['path'].'/'.$taglib.'/'.$tag.'/__server.php';
				$class='Server';
			}else{
				$actionFilePath = @self::load('routine')->get_action_file($class);
			}
			include $actionFilePath;
			if(!class_exists($class)) exit('action class not defined');
			$phphandClass=$class;
			$obj=new $phphandClass();
			
			if(!$method)
				$method=$env->get('method');
			else
				$env->set('method',$method);
			
			if(!method_exists($obj,$method)) exit('method `'.$method.'` not exists');
			self::$_action=$obj;
			//self::$_intoHTML=PHPHand_Config::getInstance()->checkHtml();
		}
		return self::$_action;
	}
	
	static function accessCheck(){
		return PAC::getInstance()->checkAccess();
	}


	/**
	 * __get方法定义
	 *
	 * 根据控制器中的$this->model_name返回一个模型的实例
	 * 如果模型实例已经存在，则直接返回该实例
	 * 如果实例不存在，则通过load方法加载该实例，并且保存在
	 * $_models数组中
	 */
	function __get($name){
		$name=strtolower($name);
		if(!isset(PHPHand::$_model[$name])){
			$this->load($name);
		}
		if(!isset(PHPHand::$_model[$name])){
			trigger_error('model `'.$name.'` not exists');
		}
		return PHPHand::$_model[$name];
	}
	
	/**
	 * load函数
	 *
	 * 加载一个模型，并且返回该模型
	 * 返回该模型的实例的路由规则是：
	 * 首先从控制器的目录同级的Model目录下根据名称搜索模型
	 * 如果模型不存在，则根据在该Model目录__extern.php中定义的
	 * 扩展目录进行搜索，如果扩展目录中仍然不存在，则在
	 * PHPHand/Model目录中进行搜索，这个目录默认是所有模型目录
	 * 的扩展目录，以保证PHPHand默认提供的模型总是有被加载的
	 * 可能
	 */
	public function load($name){
		if(in_array($name,array('env','routine'))){
			/**
			 * 由于routine和env模型的特殊性，这两个模型都要进行特殊加载
			 * env模型的特殊调用方式，是为了防止死循环
			 */
			if(!isset(PHPHand::$_model[$name])){
				include_once PHPHAND_DIR.'/model/'.$name.'.model.php';
				$model=strtoupper($name[0]).substr($name,1).'Model';
				PHPHand::$_model[$name]=new $model();
			}
		}
		//防止同一个模型被多次加载
		if(isset(PHPHand::$_model[strtolower($name)])){
			return PHPHand::$_model[strtolower($name)];
		}
		
		if(strpos($name,'.')>0)
		{
			#加载控件专属模型
			list($taglib,$tag,$model) = explode('.',$name);
			$dir = @self::load('routine')->get_tag_dir($taglib,$tag);
			if(!$dir) exit('Tag server does not exists');
			$model_path = $dir['path'].'/'.$taglib.'/'.$tag.'/'.$model.'.model.php';
			include_once $model_path;
			$modelClass=$taglib.'_'.$tag.'_'.$model.'Model';
			$obj=new $modelClass(@$this);
			PHPHand::$_model[$name]=$obj;
			return PHPHand::$_model[$name];
		}
		$model_path=self::load('routine')->get_model_file($name);
		
		if($model_path && file_exists($model_path)){
			//self::load('sql_debugger')->out_print($model_path);
			try{
				include_once $model_path;
				if(!class_exists($name.'Model')){
					trigger_error('model class '.$name.'Model not exists');
				}else{
					$modelClass=$name.'Model';
					$obj=new $modelClass(@$this);
					PHPHand::$_model[$name]=$obj;
					return PHPHand::$_model[$name];
				}
			}catch(Error $e){
				var_dump($e);
				exit('Model File Error:`'.$model_path.'`');
			}
		}
		
		//虚模型，就是没有定义的模型，可以根据模型的名称索引相关的数据表进行操作
		if(isset($this)){
			if(self::load('config')->get('db_pre') && strpos($name,self::load('config')->get('db_pre'))!==0)
			{
				$pre = self::load('config')->get('db_pre');
			}else{
				$pre = '';
			}
			$obj=new PHPHand_Model($this,strtolower($name));
			PHPHand::$_model[$name]=$obj;
			return PHPHand::$_model[$name];
		}else{
			return false;
		}
	}
	
	static function error_handler($errno,$errstr,$errfile,$errline,$errcontext)
	{
		if(!error_reporting()) return true;
		
		switch($errno)
		{
			case 2:
				//echo "[$errno] $errstr :";
				//echo '<a href="/dev.php?class=debug&path='.$errfile.'" target="_blank" style="background:#0f3;">'.$errfile.'</a>'." on line : $errline<br/>\n";
				break;
			case 8:
				//echo "[$errno] $errstr :";
				//echo '<a href="/dev.php?class=debug&path='.$errfile.'" target="_blank" style="background:#0f3;">'.$errfile.'</a>'." on line : $errline<br/>\n";
			break;
		}
	}
}

?>