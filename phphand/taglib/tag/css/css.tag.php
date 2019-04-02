<define:src type="*" /><?php
if(!isset($tag_css_target_content)){
	$tag_css_target_path=$this->env->get('app_dir').'/view/';
	if(!is_dir($tag_css_target_path)) mkdir($tag_css_target_path);
	$tag_css_target_path.=$this->config->get('style').'/';
	if(!is_dir($tag_css_target_path)) mkdir($tag_css_target_path);
	$tag_css_target_path.='public/';
	if(!is_dir($tag_css_target_path)) mkdir($tag_css_target_path);
	$tag_css_target_path.='css/';
	if(!is_dir($tag_css_target_path)) mkdir($tag_css_target_path);

	$tag_css_target_file=$tag_css_target_path.'tag' . '.css';
	if(!file_exists($tag_css_target_file)) file_put_contents($tag_css_target_file,'/** phphand tag css file */');
	
	$tag_css_target_content=file_get_contents($tag_css_target_file);
}
if(!strpos($tag_css_target_content,$param.src)){
	if(file_exists($param.src)){
		$tag_css_content=file_get_contents($tag_path);
		$tag_css_target_content.="\r\n\r\n/**". $tagurl ."*/\r\n".$tag_css_content;
		file_put_contents($tag_css_target_file,$tag_css_target_content);
	}
}
?>