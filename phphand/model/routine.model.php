<?php
/**
 * PHPHand 路由模型
 * 该模型是系统的核心模型，关联整个系统的路由结构
 * 该模型不可删除，否则PHPHand系统将无法使用
 */
class RoutineModel extends PHPHand{
	public $actionConfigFile = NULL;
	public $modelConfigFile = NULL;
	public $taglibConfigFile = NULL;
	
	//获取控制器的路由函数
	function get_action_file($class){
		$class=strtolower($class);
		$actionFilePath = $this->env->get('app_dir').'/action/'.$class.'.php';
		if(!file_exists($actionFilePath)){
			if($this->actionConfigFile===NULL){
				if(file_exists($this->env->get('app_dir').'/action/_routine/'.$_SERVER['HTTP_HOST'].'.php'))
				{
					$this->actionConfigFile = $this->env->get('app_dir').'/action/_routine/'.$_SERVER['HTTP_HOST'].'.php';
				}elseif(file_exists($this->env->get('app_dir').'/action/_routine.php')){
					$this->actionConfigFile = $this->env->get('app_dir').'/action/_routine.php';
				}else{
					$this->actionConfigFile = '';
				}
			}
			$r=false;
			if($this->actionConfigFile){
				include $this->actionConfigFile;
				foreach($_routine as $actionFolder){
					$actionFilePath = $this->env->get('app_dir').'/action/'.$actionFolder.'/'.$class.'.php';
					if(file_exists($actionFilePath)){
						define('ACTION_FILE',$actionFilePath);
						$r=true;
						break;
					}
				}
			}
			if(!$r) exit('PHPHand Error :Controller[action] File `'.$class.'` does not exists.');
		}
		return $actionFilePath;
	}
	
	//获取模型路由
	function get_model_file($name){
		$dir_list=array($this->env->get('app_dir').'/model');
		if(is_null($this->modelConfigFile))
		{
			if(@file_exists($dir_list[0].'/_routine/'.$_SERVER['HTTP_HOST'].'.php')){
				$this->modelConfigFile = $dir_list[0].'/_routine/'.$_SERVER['HTTP_HOST'].'.php';
			}elseif(file_exists($dir_list[0].'/_routine.php'))
			{
				$this->modelConfigFile = $dir_list[0].'/_routine.php';
			}else{
				$this->modelConfigFile = '';
			}
		}
		if($this->modelConfigFile){
			try{
				include $this->modelConfigFile;
				foreach($_routine as $dir){
					$dir_list[]=$dir_list[0].'/'.$dir;
				}
			}catch(Exception $e){
				echo 'Model dir config file:`'.$dir_list[0].'/_routine.php` has error';
			}
		}
		$dir_list[]=PHPHAND_DIR.'/model';
		foreach($dir_list as $model_dir){
			$model_path=$model_dir.'/'.$name.'.model.php';
			if(file_exists($model_path)){
				if(isset($_SESSION['debugger']) && $_SESSION['debugger']=='phphand')
				{
					echo '<div style="background:#cfc;border:1px solid #060;color:#060">';
					echo $model_path;
					echo '</div>';
				}
				return $model_path;
			}
		}
		
		return false;
	}
	
	function get_tag_dir($taglib,$tag)
	{
		$dir_list=array(
			array(
				'path'=>$this->env->get('app_dir').'/taglib',
				'url'=>'__WEB__/'.$this->env->get('app').'/taglib',
			)
		);
		if(is_null($this->taglibConfigFile))
		{
			if(file_exists($dir_list[0]['path'].'/_routine/'.$_SERVER['HTTP_HOST'].'.php')){
				$this->taglibConfigFile = $dir_list[0]['path'].'/_routine/'.$_SERVER['HTTP_HOST'].'.php';
			}elseif(file_exists($dir_list[0]['path'].'/_routine.php'))
			{
				$this->taglibConfigFile = $dir_list[0]['path'].'/_routine.php';
			}else{
				$this->taglibConfigFile = '';
			}
		}
		
		if($this->taglibConfigFile){
			try{
				include $this->taglibConfigFile;
				foreach($_routine as $dir){
					$dir_list[]=array(
						'path'=>$dir_list[0]['path'].'/'.$dir,
						'url'=>$dir_list[0]['url'].'/'.$dir,
					);
				}
			}catch(Exception $e){
				echo 'tag dir config file:`'.$dir_list[0].'/config.php` has error';
			}
		}
		$dir_list[]=array(
			'path'=>PHPHAND_DIR.'/taglib',
			'url'=>'__PHPHAND__/taglib',
		);
		$dir=NULL;
		foreach($dir_list as $k => $tag_dir){
			$tag_path=$tag_dir['path'].'/'.$taglib.'/'.$tag;
			if(file_exists($tag_path) && is_dir($tag_path)){
				$dir=$tag_dir;
				break;
			}
		}
		if(is_null($dir)) return false;
		return $dir;
	}
}