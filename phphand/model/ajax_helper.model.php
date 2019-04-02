<?php
class Ajax_helperModel extends PHPHand
{
	function goto_next_page($page)
	{
		$html ="<html><meta charset=\"utf-8\" /><body><div id=\"js\">";
		$html.="$(\"div[flag='\"+$('.main-tab-bar li.cur').attr('flag')+\"']\").find('input[page={$page}]').attr('check','yes').click();";
		$html .="</div></body></html>";
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}
	
	function write_js($js,$array=array()){
		if(defined('ACTION_FILE')){
			$path=dirname(ACTION_FILE).'/../view/'.strtolower($this->env->get('class')).'/'.$js.'.js';
		}else{
			$path=$this->env->get('app_dir').'/view/'.$this->config->get('style').'/'.strtolower($this->env->get('class')).'/'.$js.'.js';
		}
		$html=file_get_contents($path);
		foreach($array as $key => $value){
			$html=str_replace("{\$$key}",$value,$html);
		}
		$html="<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body><div id=\"js\">".$html."</div></body></html>";
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}

	function refresh($data_id)
	{
		$path=dirname(__FILE__).'/ajax_helper/refresh.js';
		$html=file_get_contents($path);
		$html=str_replace('[data_id]',$data_id,$html);
		$html  = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body><div id=\"js\">".$html."</div></body></html>";
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}

	function remove($data_id)
	{
		$path=dirname(__FILE__).'/ajax_helper/delete.js';
		$html=file_get_contents($path);
		$html=str_replace('[data_id]',$data_id,$html);
		if(sizeof($_POST)==0 || !isset($_POST['ajaxpost'])){
			$html  = "<script>".$html."</script>";
		}else{
			$html = "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body><div id=\"js\">".$html."</div></body></html>";
		}
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}
	
	function get_absolute_page(){
		return urlencode($this->view->_staticList['__page_'.'_']);
	}
	
	function init_ajax_page(){
		$this->view->sign('jqueryBasic',true);
		$this->view->setCachePrefix('ajax_');
		$this->view->setType('plain');
		
		$page = $this->query->get('__page__');
		if(!$page){
			//$page = "$(\"div[flag='\"+$('.main-tab-bar li.cur').attr('flag')+\"']\")";
			$page = str_replace('.','_',uniqid().microtime(true).rand(100,999));
			$this->view->sign('create_page_instance',$page);
			$page = "$('#$page')";
		}
		else{
			$page = urldecode($page);
		}
		$this->view->sign('ajax_page_var',$page);
		
		$this->view->defineStaticVar('__page__','<?php echo $this->view->get_var("ajax_page_var");?>');
		$this->view->setSubLayout(dirname(__FILE__).'/ajax_helper/init_ajax_page');
	}
}