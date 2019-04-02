<?php
$css_string="{static:css}";

$css_array=explode('[csstoken]',$css_string);
$f_array=array();
foreach($css_array as $href){
	$href=trim($href);
	if(!$href) continue;
	if(preg_match("/(^.+?)\/([^\/]+?$)/i",$href,$match)){
		$href=$this->action->dir_helper->clean($match[1]).'/'.$match[2];
	}
	if($href && !in_array($href,$f_array)){
		$f_array[]=$href;
	}
}
$css_content='';
foreach($f_array as $f){

	if(strpos($f,'.css')>0)
	{
		if($css_content!='') $css_content.='|';
		$css_content.=$f;//"<link rel=\"stylesheet\" type=\"text/css\" href=\"$f\" />\r\n";
	}else if(strpos($f,'.js')>0){
		echo "<script type=\"text/javascript\" src=\"$f\"></script>";
	}
}
//$css_final_file=str_replace(__ROOT__,'',$this->action->dir_helper->clean(__ROOT__.'/app/wallet/css')).'/'.md5($css_string).'.css';
//file_put_contents(__ROOT__.'/'.$css_final_file,$css_content);
?><link rel="stylesheet" type="text/css" href="?class={base:csshock}&method=css&f={$css_content}" /><base:css src="__TAG__/layout.css" />