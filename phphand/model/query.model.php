<?php
class QueryModel extends PHPHand{
	private $_data=array();
	
	private $inited=false;
	function init(){
		$this->inited = true;
		if(!$this->config->get('virtual_path')){
			/**
			 * if not virtual path mode
			 * initialize query data by $_GET
			 */
			$this->_data=array_merge($this->_data,array_change_key_case($_GET));
			return;
		}
		$querystring=str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['REQUEST_URI']);
		$querystring=str_replace('?','/',str_replace('&','/',str_replace('=','--',$querystring)));
		if($querystring==$_SERVER['REQUEST_URI']) return ;
		$data=explode("/",$querystring);
		foreach($data as $key => $item){
			$nameValuePair=explode("--",$item);
			$size=sizeof($nameValuePair);
			if($nameValuePair[0]){
				$nameValuePair[0]=urldecode($nameValuePair[0]);
				if($size==2){
					$this->add(strtolower($nameValuePair[0]),$nameValuePair[1]);
				}else{
					if($key==1){
						$this->add('class',$nameValuePair[0]);
					}elseif($key==2){
						$this->add('method',$nameValuePair[0]);
					}else{
						$val = preg_replace('/^'.$nameValuePair[0].'\-\-/is','',$item);
						$this->add(strtolower($nameValuePair[0]),$val);
					}
				}
			}
		}
	}
	
	/**
	 * get
	 *
	 * get a query value
	 *
	 * @param name String,NULL
	 */
	function get($name=null){
		if(!$this->inited) $this->init(self::$app_name);

		if(!$name) return $this->_data;
		$name=strtolower($name);
		if(isset($this->_data[$name])) return $this->_data[$name];
		switch($name){
			case 'class':
				return $this->config->get('default_class');
			case 'method':
				return $this->config->get('default_method');
		}
		return false;
	}
	
	/**
	 * set
	 */
	function add($name,$value){
		if(!$this->inited) $this->init(self::$app_name);
		if(substr($name,-2,2)=='[]')
		{
			$name = substr($name,0,strlen($name)-2);
			if(!isset($this->_data[strtolower($name)]))
			{
				$this->_data[strtolower($name)]=array();
			}
			$this->_data[strtolower($name)][]=$value;
		}else{
			$this->_data[strtolower($name)]=$value;
		}
	}
}