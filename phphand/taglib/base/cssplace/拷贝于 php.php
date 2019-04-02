<?php
$css_string="{static:css}";
if(strpos($css_string,'[csstoken]')===0){
	$css_string=substr($css_string,10);
}
$css_array=explode('[csstoken]',$css_string);
$f_array=array();
foreach($css_array as $href){
	$href=trim($href);
	if(preg_match("/(^.+?)\/([^\/]+?$)/i",$href,$match)){
		$href=$this->action->dir_helper->clean($match[1]).'/'.$match[2];
	}
	if($href && !in_array($href,$f_array)){
		$f_array[]=$href;
	}
}
$css_content='';
foreach($f_array as $f){
	$css_content.="@import url('$f');\r\n";
}
$css_final_file=str_replace(__ROOT__,'',$this->action->dir_helper->clean(__ROOT__.'/shop/wallet/css')).'/'.md5($css_string).'.css';
file_put_contents(__ROOT__.'/'.$css_final_file,$css_content);
?><link rel="stylesheet" type="text/css" href="__WEB__{$css_final_file}" /><sh:css src="__TAG__/basic.css" />