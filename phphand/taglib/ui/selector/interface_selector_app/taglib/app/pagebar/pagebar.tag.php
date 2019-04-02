<define:size type="int" required="false" default="2" />
<?
$pageformat="{___phphand_page}";
$pagestr='';
if($___phphand_page <> 0 && $___phphand_pagecount > 0){
	if($___phphand_page==1 && $___phphand_pagecount==1) $pagestr='';
	if($___phphand_page>1){
		$pagestr.="<a href=\"".str_replace('{___phphand_page}',1,$pageformat)."\">首页</a>";
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
			$pagestr.="<a href=\"".str_replace('{___phphand_page}',$i,$pageformat)."\">[$i]</a>";
		}
	}
	if($___phphand_page<$___phphand_pagecount){
		$pagestr.="<a href=\"".str_replace('{___phphand_page}',$___phphand_pagecount,$pageformat)."\">尾页</a>";
	}
}
?>
<? if($pagestr!='' && $___phphand_pagecount>1){?>
<link rel="stylesheet" type="text/css" href="__TAG__/style/pagebar.css">
<? echo '<p class="interface_selector_pagebar">'.$pagestr.'</p>';?>
<?
}
?>