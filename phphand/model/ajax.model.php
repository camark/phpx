<?php
class AjaxModel extends PHPHand_Model{
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
	
	function refresh(){
		if(defined('ACTION_FILE')){
			$path=dirname(ACTION_FILE).'/../view/'.$this->env->get('class').'/'.$js.'.js';
		}else{
			$path=__ROOT__.'/admin/view/'.$this->config->get('style').'/'.strtolower($this->env->get('class')).'/'.$js.'.js';
		}
		$html="<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body><div id=\"js\">location.reload();</div></body></html>";
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}
	
	function goto_url($url){
		$path=__ROOT__.'/admin/view/'.$this->config->get('style').'/'.strtolower($this->env->get('class')).'/'.$js.'.js';
		$html="<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body><div id=\"js\">location.href='$url';</div></body></html>";
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}
	
	function post($url,$data=array()){
		$html="<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body><div id=\"js\">";
		$html.="$('<'+'form method=post name=ajaxpostform style=display:none; action=$url>";
		foreach($data as $key => $value){
			$html.="<'+'input type=hidden name=\"$key\" value=\"$value\" />";
		}
		$html.="</form>').appendTo($('body'));$('form[name=ajaxpostform]')[0].submit();";
		$html.="</div></body></html>";
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}
	
	function message($msg,$url=''){
		$js="alert('".str_replace("'","\\'",$msg)."');";
		if($url!=''){
			$js.="location.href='".str_replace("'","\\'",$url)."';";
		}
		$html="<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body><div id=\"js\">".$js."</div></body></html>";
		ob_clean();
		header('content-type:text/html;charset=utf-8');
		echo $html;
		exit;
	}
}