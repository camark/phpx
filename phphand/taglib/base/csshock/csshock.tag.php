<?php
ini_set('pcre.backtrack_limit', 999999999);
$css_file_name = base64_encode($_SERVER['HTTP_HOST'].'$'.$_SERVER['SCRIPT_NAME'] . '$' . $this->config->get('style')) . '.css';
$doc_root=str_replace('\\','/',str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['SCRIPT_FILENAME']));
$css_config_path = $doc_root  . '__TAG__/cache/' . $css_file_name . '.php';
$css_config = $this->data_helper->read($css_config_path);
if(!$css_config)
{
	$css_config=array();
}

$css_string="{static:css}";
$css_array=explode('[csstoken]',$css_string);
$f_array=array();
foreach($css_array as $href){
	$href=trim($href);
	if(!$href) continue;
	if(preg_match("/(^.+?)\/([^\/]+?$)/i",$href,$match)){
		$href=$this->dir_helper->clean($match[1]).'/'.$match[2];
	}
	if($href && !in_array($href,$f_array)){
		$f_array[]=$href;
	}
}
$css_content='';
$js_code='';

foreach($f_array as $f){
	if(strpos($f,'.css')>0)
	{
		//if($css_content!='') $css_content.='|';
		//$css_content.="<link rel=\"stylesheet\" type=\"text/css\" href=\"$f\" />\r\n";//str_replace('/','$',$f);//"<link rel=\"stylesheet\" type=\"text/css\" href=\"$f\" />\r\n";
		if(strpos($f,'http://')===0)
		{
			if(strpos($f,'http://'.$_SERVER['HTTP_HOST'])==0)
			{
				$f = str_replace('http://'.$_SERVER['HTTP_HOST'],'',$f);
			}
		}else if(strpos($f,'https://')===0){
			if(strpos($f,'https://'.$_SERVER['HTTP_HOST'])==0)
			{
				$f = str_replace('https://'.$_SERVER['HTTP_HOST'],'',$f);
			}
		}

		if(!isset($css_config[$f]) || strpos($f,'http')===false && filemtime($doc_root . $f)>$css_config[$f])
		{
			if(!isset($css_exploded))
			{
				if(!file_exists($doc_root . '__TAG__/cache/' . $css_file_name)){
					$css_exploded = array(array(),array());
				}else{
					$css_content = file_get_contents($doc_root . '__TAG__/cache/' . $css_file_name);
					$css_exploded = explode("\n/*-----------------------------------------CSS-IMPORT-END---------------------------------------*/\n",$css_content);
					preg_match_all("/\/\*css\-hock\-css\-begin:(.+?)\*\/([\s\S]+?)\/\*css\-hock\-css\-end\*\//is",$css_exploded[0],$matches,PREG_SET_ORDER);
					$array=array();
					foreach($matches as $match)
					{
						$array[$match[1]]=$match[2];
					}
					$css_exploded[0]=$array;
					preg_match_all("/\/\*css\-hock\-css\-begin:(.+?)\*\/([\s\S]+?)\/\*css\-hock\-css\-end\*\//is",$css_exploded[1],$matches,PREG_SET_ORDER);
					
					$array=array();
					foreach($matches as $match)
					{
						$array[$match[1]]= "/*css-hock-css-begin:" . $match[1] ."*/\n" . $match[2] . "\n/*css-hock-css-end*/\n";
					}
					$css_exploded[1]=$array;
				}
			}


			if(strpos($f,'http')===false)
			{
				$content = file_get_contents($doc_root . $f);
				$content = $this->{'base.csshock.helper'}->parse_css($content,$f);
				$css_exploded[1][$f] = "/*css-hock-css-begin:$f*/\n" . $content . "\n/*css-hock-css-end*/\n";
				$css_config[$f] = filemtime($doc_root . $f);
			}else{
				$css_exploded[0][$f] = "/*css-hock-css-begin:$f*/\n" . "@import url('$f');" . "\n/*css-hock-css-end*/\n";
				$css_config[$f] = time();
			}
		}
	}else if(strpos($f,'.js')>0){
		$js_code.="<script type=\"text/javascript\" src=\"". $f . "\"></script>";
	}
}
if(isset($css_exploded))
{
	$css_content = implode("\n\n",$css_exploded[0]) . "\n/*-----------------------------------------CSS-IMPORT-END---------------------------------------*/\n" . implode("\n\n",$css_exploded[1]);

	file_put_contents($doc_root . '__TAG__/cache/' . $css_file_name,$css_content);
	$this->data_helper->write($css_config_path,$css_config);
}
if(!function_exists('is_HTTPS')){
	function is_HTTPS(){  //判断是不是https
		return true;
	            if(!isset($_SERVER['HTTPS']))  return FALSE;  
	            if($_SERVER['HTTPS'] === 1){  //Apache  
	                return TRUE;  
	            }elseif($_SERVER['HTTPS'] === 'on'){ //IIS  
	                return TRUE;  
	            }elseif($_SERVER['SERVER_PORT'] == 443){ //其他  
	                return TRUE;  
	            }  
	                return FALSE;  
	   }  
	
}
//$css_final_file=str_replace($doc_root,'',$this->action->dir_helper->clean($doc_root.'/app/wallet/css')).'/'.md5($css_string).'.css';
//file_put_contents($doc_root.'/'.$css_final_file,$css_content);
?><link rel="stylesheet" type="text/css" href="__TAG__/cache/{$css_file_name}?r=<?php echo time();?>" /><base:css src="__TAG__/layout.css" />{$js_code}