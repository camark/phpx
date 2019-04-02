<define:size type="int" required="false" default="5" />
<?php
$pageformat='';
$pagestr='';
$flag=false;
$get=$this->query->get();
if(PHPHand::$_intoHTML){
	
}else if($this->config->get('VIRTUAL_PATH')){
	foreach($get as $name => $value){
		if($name=='page'){
			if($pageformat=='') $pageformat=$this->env->get('php_file').'/page-{___phphand_page}';
			else $pageformat.='/page-{___phphand_page}';
			$flag=true;
		}else{
			if($pageformat=='') $pageformat=$this->env->get('php_file')."/$name-$value";
			else $pageformat.="/$name-$value";
		}
	}
	if(!$flag){
		if($pageformat=='') $pageformat=$this->env->get('php_file').'/page-{___phphand_page}';
		else $pageformat.='/page-{___phphand_page}';
	}
}else{
	foreach($get as $name => $value){
		if($name=='page'){
			if($pageformat=='') $pageformat='?page={___phphand_page}';
			else $pageformat.='&page={___phphand_page}';
			$flag=true;
		}else{
			if($pageformat=='') $pageformat="?$name=$value";
			else $pageformat.="&$name=$value";
		}
	}
	if(!$flag){
		if($pageformat=='') $pageformat='?page={___phphand_page}';
		else $pageformat.='&page={___phphand_page}';
	}
}
if(isset($___phphand_page) && $___phphand_page <> 0 && $___phphand_pagecount > 0){
	if($___phphand_page==1 && $___phphand_pagecount==1) $pagestr='';
	if($___phphand_page>1){
		//$pagestr.="<a href=\"".str_replace('{___phphand_page}',1,$pageformat)."\">第一页</a>";
		$pagestr.="<a href=\"".str_replace('{___phphand_page}',$___phphand_page-1,$pageformat)."\">&lt;</a>";
	}
	
	if($___phphand_page-1 <= $param.size){
		$x=1;
	}else{
		$x=$___phphand_page-$param.size;
	}
	if($___phphand_pagecount-$___phphand_page <= $param.size){
		$y=$___phphand_pagecount;
	}else{
		$y=$___phphand_page+$param.size;
	}
	for($i=$x;$i<=$y;$i++){
		if($i==$___phphand_page){
			$pagestr.="<span>$i</span>";
		}else{
			$pagestr.="<a href=\"".str_replace('{___phphand_page}',$i,$pageformat)."\">$i</a>";
		}
	}
	if($___phphand_page<$___phphand_pagecount){
		$pagestr.="<a href=\"".str_replace('{___phphand_page}',$___phphand_page+1,$pageformat)."\">&gt;</a>";
		//$pagestr.="<a href=\"".str_replace('{___phphand_page}',$___phphand_pagecount,$pageformat)."\">最末页</a>";
	}
}
?>
<?php if($pagestr!='' && $___phphand_pagecount>1){?>
<link rel="stylesheet" type="text/css" href="__TAG__/style/pagebar.css">
<?php echo '<p id="pagebar">'.$pagestr.'</p>';?>
<?php
}
?>