<?php
class PluginModel extends PHPHand_Model{
	function get_all($type='*'){
		$plugins=array();
		foreach(glob(__ROOT__.'/plugin/*') as $plugin_path){
			unset($plugin);
			$define_path=$plugin_path."/define.php";
			if(file_exists($define_path)){
				try{
					include $define_path;
					
					preg_match('/\/([^\/]+?)$/is',$plugin_path,$match);
					$plugin_name = $match[1];
					
					if(file_exists(dirname($define_path).'/logo.png'))
					{
						$logo = $this->env->get('app_url').'/plugin/'.$plugin_name.'/logo.png';
					}else{
						$logo = '';
					}
					
					if(class_exists($plugin_name)){
						$model = new $plugin_name();
						
						$plugins[$plugin_name] = array(
							'name' => $model->name,
							'model' => $model,
							'logo' => $logo,
							'button_line' => $model->button_line,
						);
					}else{
						$plugins[$plugin_name] = array(
							'name' => $plugin_name,
							'model' => NULL,
							'logo' => $logo,
							'button_line' => '',
						);
					}
					/*
					if(isset($plugin)){
						if(isset($plugin['type']) && $plugin['type']==$type || $type=='*'){
							preg_match("/\/([^\/]+?)$/is",$plugin_path,$match);
							$folder=$match[1];
							$plugins[strtolower($folder)]=$plugin+array('installed' => false);
						}
					}else{
						
					}*/
				}catch(Exception $e){
				}
			}
		}
		$sql="SELECT * FROM pre_plugin WHERE host='".$_SERVER['HTTP_HOST']."'";
		$query=$this->db->query($sql);
		while($plugin=@mysql_fetch_array($query)){
			$folder=$plugin['name'];
			if(isset($plugins[$folder])){
				$plugins[$folder]['installed']=true;
			}
		}
		return $plugins;
	}
	
	function remove($plugin_id=NULL){
		if(is_null($plugin_id)){
			$plugin_id=$this->latest_id;
		}
		
		$sql="DELETE FROM pre_plugin WHERE plugin_id=$plugin_id";
		$this->db->query($sql);
		$sql="DELETE FROM pre_plugin_job WHERE plugin_id=$plugin_id";
		$this->db->query($sql);
	}
	
	function link_folder($src_path,$dest_path,$config_file_name='_routine.php'){
		$path=$this->compare_path($dest_path,$src_path);
		
		if(file_exists($dest_path.'/_routine/'.$_SERVER['HTTP_HOST'].'.php'))
		{
			$config_file_name = '_routine/'.$_SERVER['HTTP_HOST'].'.php';
		}
		include $dest_path.'/'.$config_file_name;
		if(!isset($_routine) || !in_array($path,$_routine)){
			$_routine[]=$path;
			$this->data_helper->write($dest_path.'/'.$config_file_name,$_routine,'_routine');
		}
	}
	
	function unlink_folder($src_path,$dest_path,$config_file_name='_routine.php'){
		$path=$this->compare_path($dest_path,$src_path);
		
		if(file_exists($desc_path.'/_routine/'.$_SERVER['HTTP_HOST'].'.php'))
		{
			$config_file_name = '_routine/'.$_SERVER['HTTP_HOST'].'.php';
		}

		include $dest_path.'/'.$config_file_name;
		if(!is_array($_routine)) $_routine=array();
		if(in_array($path,$_routine)){
			$new_extern_dir=array();
			foreach($_routine as $p){
				if($p!=$path && !in_array($p,$new_extern_dir)){
					$new_extern_dir[]=$p;
				}
			}
			$this->data_helper->write($dest_path.'/'.$config_file_name,$new_extern_dir,'_routine');
		}
	}
	
	//扩展ACTION目录
	function link_action_folder($path){
		$path=$this->compare_path($this->env->get('app_dir').'/action',$path);
		include $this->env->get('app_dir').'/action/_routine.php';
		if(!in_array($path,$extern_dir)){
			$extern_dir[]=$path;
			$this->action->data_helper->write($this->env->get('app_dir').'/action/__extern.php',$extern_dir,'extern_dir');
		}
	}
	function unlink_action_folder($path){
		$path=$this->compare_path($this->env->get('app_dir').'/action',$path);
		include $this->env->get('app_dir').'/action/__extern.php';
		if(in_array($path,$extern_dir)){
			$new_extern_dir=array();
			foreach($extern_dir as $p){
				if($p!=$path){
					$new_extern_dir[]=$p;
				}
			}
			$this->action->data_helper->write($this->env->get('app_dir').'/action/__extern.php',$new_extern_dir,'extern_dir');
		}
	}
	
	//扩展TAGLIB目录(相对ADMIN和SHP)
	function link_taglib_folder($path){
		$xpath=$this->compare_path(__ROOT__.'/../SHP/taglib',$path);
		include __ROOT__.'/../SHP/taglib/config.php';
		if(!in_array($xpath,$extern_dir)){
			$extern_dir[]=$xpath;
			$this->action->data_helper->write(__ROOT__.'/../SHP/taglib/config.php',$extern_dir,'extern_dir');
		}

		$xpath=$this->compare_path(__ROOT__.'/ADM/taglib',$path);
		include __ROOT__.'/ADM/taglib/config.php';
		if(!in_array($xpath,$extern_dir)){
			$extern_dir[]=$xpath;
			$this->action->data_helper->write(__ROOT__.'/ADM/taglib/config.php',$extern_dir,'extern_dir');
		}
	}
	function unlink_taglib_folder($path){
		$xpath=$this->compare_path(__ROOT__.'/../SHP/taglib',$path);
		include __ROOT__.'/../SHP/taglib/config.php';
		if(in_array($xpath,$extern_dir)){
			$new_extern_dir=array();
			foreach($extern_dir as $p){
				if($p!=$xpath){
					$new_extern_dir[]=$p;
				}
			}
			$this->action->data_helper->write(__ROOT__.'/../SHP/taglib/config.php',$new_extern_dir,'extern_dir');
		}
		
		$xpath=$this->compare_path(__ROOT__.'/ADM/taglib',$path);
		include __ROOT__.'/ADM/taglib/config.php';
		if(in_array($xpath,$extern_dir)){
			$new_extern_dir=array();
			foreach($extern_dir as $p){
				if($p!=$xpath){
					$new_extern_dir[]=$p;
				}
			}
			$this->action->data_helper->write(__ROOT__.'/ADM/taglib/config.php',$new_extern_dir,'extern_dir');
		}
	}
	//扩展Model目录（针对Admin）
	function link_model_folder($path){
		$path=$this->compare_path($this->env->get('app_dir').'/action',$path);
		include $this->env->get('app_dir').'/action/__extern.php';
		if(!in_array($path,$extern_dir)){
			$extern_dir[]=$path;
			$this->action->data_helper->write($this->env->get('app_dir').'/action/__extern.php',$extern_dir,'extern_dir');
		}
	}
	function unlink_model_folder($path){
		$path=$this->compare_path($this->env->get('app_dir').'/model',$path);
		include $this->env->get('app_dir').'/model/_routine.php';
		if(in_array($path,$_routine)){
			$new_extern_dir=array();
			foreach($_routine as $p){
				if($p!=$path){
					$new_extern_dir[]=$p;
				}
			}
			$this->action->data_helper->write($this->env->get('app_dir').'/model/_routine.php',$new_extern_dir,'_routine');
		}
	}
	
	
	//获取相对路径
	function compare_path($path_a, $path_b) {
		$path_a=str_replace("\\","/",$path_a);
		$path_b=str_replace("\\","/",$path_b);
		$array_a =explode('/', $path_a);
		$array_b =explode('/', $path_b);

		$a_len =count($array_a);
		$b_len =count($array_b);
		for ( $i =0; $i < $a_len; $i++ ) {
			if ($array_a[$i] != $array_b[$i] ) {
				break;
			}
		}
		$com_path ="";
		for ( $j =0; $j < $a_len - $i; $j++ ) {
			$com_path .='../';
		}
		for ( $i; $i< $b_len; $i++ ) {
			$com_path .=$array_b[$i] . '/';
		}
		$com_path=preg_replace("/\/$/is","",$com_path);
		return $com_path;
	}
	
	/**
	 * 添加工作点
	 */
	function add_job($list=array(),$plugin){
		foreach($list as $job_position => $job_file){
			$test = $this->max_plugin_job->none_pre()->get_by(array(
				'position' => $job_position,
				'file' => $job_file,
				'host' => $_SERVER['HTTP_HOST'],
				'plugin' => $plugin,
			));
			if(!$test){
				$this->max_plugin_job->none_pre()->_insert(array(
					'position' => $job_position,
					'file' => $job_file,
					'host' => $_SERVER['HTTP_HOST'],
					'plugin' => $plugin,
				));
			}
		}
	}
}