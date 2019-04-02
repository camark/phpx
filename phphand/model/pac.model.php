<?php

/**
 * PAC is a basic scurity for PHPHand
 */
class PacModel extends PHPHand{
	
	/** instance handle */
	private static $_instance=null;
	/**
	 * constructor
	 */
	function __construct(){
		self::$_instance=$this;
		parent::__construct();
	}
	/** get instance */
	public static function getInstance(){
		if(!self::$_instance){
			new self();
		}
		return self::$_instance;
	}

	function getDatabaseTableStatus()
	{
		$query=$this->db->query('show table status');
		$result=array();
		while($tb=$this->db->fetchArray($query)){
			$result[$tb['Name']]=$tb;
		}
		unset($tb,$query);
		return $result;
	}
	/**
	 * buildDb
	 *
	 * build a basic database structure for PAC
	 * including following tables:
	 * - phphand_member
	 * - phphand_group
	 * - phphand_action
	 * if database not exists this method would be canceled
	 */
	private function buildDb(){
		if(!$this->share){
			include dirname(__FILE__).'/../Cache/PHPHand_Share.php';
			$this->share=PHPHand_Share::getInstance();
		}
		$database=$this->share->get('Database');
		if(is_null($database)){
			$database=$this->getDatabaseTableStatus();
			$this->share->set('Database',$database);
		}
		$rebuild=false;
		if(!isset($database['phphand_member_group'])){
			$r=$this->db->query("CREATE TABLE IF NOT EXISTS `phphand_member_group` (`id` int(6) NOT NULL auto_increment,`name` varchar(20) NOT NULL,`type` enum('guest','common','admin') NOT NULL,`db_access` text,`actions` text,PRIMARY KEY  (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			if($r){
				$this->db->query("INSERT into phphand_member_group(id,name,type) values(1,'会员','common');");
				$this->db->query("INSERT into phphand_member_group(id,name,type) values(2,'管理员','admin');");
			}
			$rebuild=true;
		}
		if(!isset($database['phphand_member'])){
			$this->db->query("CREATE TABLE IF NOT EXISTS `phphand_member`(`id` int(10) NOT NULL auto_increment,`name` varchar(60) NOT NULL,`password` varchar(32) NOT NULL,`groupid` int(6) NOT NULL default 2,`picture` varchar(30) NULL,PRIMARY KEY  (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			$rebuild=true;
		}
		if(!isset($database['phphand_action'])){
			$this->db->query("CREATE TABLE IF NOT EXISTS `phphand_action` (`id` int(10) NOT NULL auto_increment,`name` varchar(50),`content` varchar(250),`open` tinyint(1) DEFAULT 1,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
			$rebuild=true;
		}
		if($rebuild){
			$database=$this->getDatabaseTableStatus();
			$this->share->set('Database',$database);
		}
	}
	/**
	 * checkAccess
	 *
	 * check if visitor has permission to do behave a action
	 *
	 * @param class String
	 * @param method String
	 */
	function checkAccess($app='',$class='',$method=''){
		/**
		 * if not set database
		 * make `true` as a default value
		 * so that people can access your website
		 */
		if(!$this->config->get('db_host')) return true;
		/** build database structure */
		$this->buildDb();
		/** check login */
		$this->checkLogin();
		if(!isset($_SESSION['groupid'])){
			$_SESSION['loged']=false;
			$_SESSION['id']=0;
			$_SESSION['groupid']=0;
			$_SESSION['grouptype']='guest';
			//$_SESSION['name']=$this->config->get('guest')?$this->config->get('guest'):'访客';
		}
		if(isset($_SESSION['grouptype']) && $_SESSION['grouptype']=='admin') return true;
		if(!$app) $app=$this->env->get('app');
		if(!$class) $class=$this->env->get('class');
		if(!$method) $method=$this->env->get('method');
		$checker=strtolower($app.'-'.$class.'-'.$method);
		$cChecker=strtolower($app.'-'.$class);

		$actionCache=$this->share->get('PHPHandActionCache');
		if(!is_array($actionCache) || true){
			$actionCache=array();
			$query=$this->db->query("SELECT * FROM phphand_action");
			while($action=@mysql_fetch_array($query)){
				if($action['open']==1){
					$array=explode("\r\n",$action['content']);
					foreach($array as $item){
						$actionCache[trim(strtolower($item))]=true;
					}
					unset($array);
				}
			}
			$this->share->set('PHPHandActionCache',$actionCache);
		}
		if(isset($actionCache[$checker]) && $actionCache[$checker]==true || isset($actionCache[$cChecker]) && $actionCache[$cChecker]==true) return true;
		
		if($_SESSION['groupid']){
			$groupPermission=$this->share->get('GroupPermission'.$_SESSION['groupid']);
			if(!is_array($groupPermission) || true){
				$query=$this->db->query("SELECT * FROM phphand_member_group where id={$_SESSION['groupid']}");
				$actions='';
				$groupDbAccess=array();
				if($group=@mysql_fetch_array($query)){
					$groupPermission['type']=$group['type'];
					$array=explode("\r\n",$group['db_access']);
					foreach($array as $item){
						$groupDbAccess[trim(strtolower($item))]=true;
					}
					$actions=$group['actions'];
					unset($group,$array);
				}else{
					trigger_error("cant find a guest group in db", E_USER_ERROR);
				}
				$groupPermissionArray=array();
				if($actions){
					$query=$this->db->query("SELECT * FROM phphand_action WHERE id in ($actions)");
					while($action=@mysql_fetch_array($query)){
						$array=explode("\r\n",$action['content']);
						foreach($array as $item){
							$groupPermissionArray[trim(strtolower($item))]=true;
						}
						unset($array);
					}
					unset($action,$query);
				}
				$groupPermission['dbAccess']=$groupDbAccess;
				$groupPermission['appAccess']=$groupPermissionArray;
				$this->share->set('GroupPermission'.$_SESSION['groupid'],$groupPermission);
			}
			if(isset($groupPermission['appAccess'][$checker]) || isset($groupPermission['appAccess'][strtolower($app.'-'.$class)])){
				$checkStep1=true;
			}else{
				$checkStep1=false;
			}
			
			if($groupPermission['type']=='admin'){
				$checkStep2=true;
			}else{
				$checkStep2=false;
			}
	
			if($checkStep2 && !$checkStep1 || (!$checkStep2 && $checkStep1)) return true;
		}
		return false;
	}
	
	function checkLogin(){
		if(isset($_POST['paclogin'])){
			if($this->config->get('login_password_required')){
				if(strtolower($_POST['CheckCode'])!=strtolower($_SESSION['PHPHAND_CheckCode'])){
					PAC::$_action->message('wrong safe code!');
				}
			}
			$name=$_POST['username'];
			$password=md5($_POST['password']);
			if(!$name || !$password) PAC::$_action->message('username and password required!');
			$user=$this->db->getOne("SELECT m.*,g.name as groupname,g.type as grouptype FROM phphand_member m,phphand_member_group g WHERE m.name='$name' AND m.password='$password' AND m.groupid=g.id");
			if($user){
				$_SESSION['id']=$user['id'];
				$_SESSION['name']=$user['name'];
				$_SESSION['groupid']=$user['groupid'];
				$_SESSION['grouptype']=$user['grouptype'];
				$_SESSION['loged']=true;
				unset($user);
				$this->view->setFolder('Public');
				if(!$this->lang){
					if(!PHPHand_Lang) include PHPHAND_DIR.'/Core/Lang/PHPHand_Lang.php';
					$this->lang=PHPHand_Lang::getInstance();
				}
				$this->view->sign('msg',$this->lang->get('login_success'));
				$this->view->sign('url',$_POST['url']);
				PAC::$_action->message('登录成功!',$_POST['url']);
				exit;
			}else{
				PAC::$_action->message('wrong name or password,failed to login!');
				exit;
			}
		}
	}
	/**
	 * login and logout
	 */
	function login($msg='login'){
		if(!$msg) $msg=$this->lang->get('please login');
		$this->view->setFolder('Public');
		$this->view->sign('msg',$msg);
		$this->view->display('login');
	}
	
	function logout(){
		session_destroy();
		$this->view->sign('msg',$this->lang->get('logout_success'));
		$this->view->setFolder('public');
		$this->view->setLayout('');
		$this->view->display('message');
	}
}
?>