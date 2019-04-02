<div class="interface_selector_box_content_list">
	<ul>
		




<?php
if(10<=0){
	exit('cant define a below 0');
}
$___phphand_sql=preg_replace("/SELECT\s(.+?)FROM/is","SELECT COUNT(*) n FROM",$this->_var['sql']);

if(PHPHand::$_intoHTML!==false){
	if(PHPHand::$_intoHTML!=''){
		$array=explode('.',PHPHand::$_intoHTML);
		$___flag='-';
		foreach($array as $i => $___pm_key){
			if($i==0) continue;
			$___flag.= $___pm_key.'-'.$this->query->get($___pm_key);
		}
	}else{
		$___flag='-';
	}
	$___html_page_data=array('sql'=>$___phphand_sql,'pagesize'=>10);
	$this->share->set('html_page_data_'.strtolower($this->env->get('class').'-'.$this->env->get('method')).'-'.$___flag,$___html_page_data);
}
$___phphand_query=$this->db->query($___phphand_sql,false);
$___phphand_mainresult=$this->db->fetchArray($___phphand_query);

if(isset($record_number) && $record_number){
	$record_number=$___phphand_mainresult['n'];
}
$___phphand_pagecount=ceil($___phphand_mainresult['n']/10);
$this->sign('page_count',$___phphand_pagecount);

$___phphand_page=intval($this->query->get('page'));
if(!$___phphand_page || $___phphand_page<1){
	$___phphand_page=1;
}elseif($___phphand_page>$___phphand_pagecount){
	$___phphand_page=$___phphand_pagecount;
}
$___phphand_list_start=($___phphand_page-1)*10;
$___phphand_list_size = 10;
$___phphand_list_sql  = $this->_var['sql'] . " LIMIT $___phphand_list_start,$___phphand_list_size";
$___phphand_mainquery = $this->db->query($___phphand_list_sql,false);
$mc=0;
while($rs = $this->db->fetchArray($___phphand_mainquery)){
$mc++;
?>

		<li value="<?php echo @$rs[$this->_var['value_column']];?>" title="<?php echo @$rs[$this->_var['show_column']];?>"><?php if(mb_strlen($rs[$this->_var['show_column']],'utf-8')>12){?><?php echo mb_substr($rs[$this->_var['show_column']],0,10,'utf-8').'..';?><?php }else{?><?php echo @$rs[$this->_var['show_column']];?><?php }?></li>
		
<?php
}
?>
		<div class="interface_selector_clear"></div>
	</ul>
	
<?
$pageformat="{___phphand_page}";
$pagestr='';
if($___phphand_page <> 0 && $___phphand_pagecount > 0){
	if($___phphand_page==1 && $___phphand_pagecount==1) $pagestr='';
	if($___phphand_page>1){
		$pagestr.="<a href=\"".str_replace('{___phphand_page}',1,$pageformat)."\">首页</a>";
	}
	
	if($___phphand_page-1 <= 2){
		$x=1;
	}else{
		$x=$___phphand_page-2;
	}
	if($___phphand_pagecount-$___phphand_page <= 2){
		$y=$___phphand_pagecount;
	}else{
		$y=$___phphand_page+2;
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
<link rel="stylesheet" type="text/css" href="/shop/phphand/taglib/ui/selector/interface_selector_app/taglib/app/pagebar/style/pagebar.css">
<? echo '<p class="interface_selector_pagebar">'.$pagestr.'</p>';?>
<?
}
?>
</div>