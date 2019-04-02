<define:time type="int" required="false" default="10000" />
<define:name type="*" required="true" />
<?php
if(!isset($___phphand_cache_count)){
	$___phphand_cache_count=0;
	$___phphand_cache_name=md5($_SERVER['REQUEST_URI']);
}
ob_flush();
$___phphand_cache_count++;
$___phphand_cache_content=$this->cache->read($___phphand_cache_name.'___'.$___phphand_cache_count,$param.time);
if($___phphand_cache_content){
	echo $___phphand_cache_content;
}else{
	?>
	__HTML__
	<?php
	$___phphand_cache_content=ob_get_contents();
	$this->cache->write($___phphand_cache_name.'___'.$___phphand_cache_count,$___phphand_cache_content);
}
ob_flush();
?>