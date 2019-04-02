<div class="interface_selector_box_content_list">
	




<?php
if(6<=0){
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
	$___html_page_data=array('sql'=>$___phphand_sql,'pagesize'=>6);
	$this->share->set('html_page_data_'.strtolower($this->env->get('class').'-'.$this->env->get('method')).'-'.$___flag,$___html_page_data);
}
$___phphand_query=$this->db->query($___phphand_sql,false);
$___phphand_mainresult=$this->db->fetchArray($___phphand_query);

if(isset($record_number) && $record_number){
	$record_number=$___phphand_mainresult['n'];
}
$___phphand_pagecount=ceil($___phphand_mainresult['n']/6);

$___phphand_page=intval($this->query->get('page'));
if(!$___phphand_page || $___phphand_page<1){
	$___phphand_page=1;
}elseif($___phphand_page>$___phphand_pagecount){
	$___phphand_page=$___phphand_pagecount;
}
$___phphand_list_start=($___phphand_page-1)*6;
$___phphand_list_size = 6;
$___phphand_list_sql  = $this->_var['sql'] . " LIMIT $___phphand_list_start,$___phphand_list_size";
$___phphand_mainquery = $this->db->query($___phphand_list_sql,false);
$n=0;
while($rs = $this->db->fetchArray($___phphand_mainquery)){
$n++;
?>

	<dl>
		<dt>
			<span value="<?php echo @$rs[$this->_var['value_column']];?>" title="<?php echo @$rs[$this->_var['show_column']];?>"><?php echo @$rs[$this->_var['show_column']];?></span>
		</dt>
		<dd>
			<?php $this->_var['sql']=str_replace('$.'.$this->_var['column'],$rs[$this->_var['column']],$this->_var['sql2']);?>
			


<?php
$___phphand_list_query=$this->db->query($this->_var['sql'],false);
$empty=true;
$n=0;
while($rs2=$this->db->fetchArray($___phphand_list_query)){
$empty=false;
$n++;
?>

			<span value="<?php echo @$rs2[$this->_var['value_column']];?>" title="<?php echo @$rs2[$this->_var['show_column']];?>"><?php echo @$rs2[$this->_var['show_column']];?></span>
			
<?php
}
?>
			<div class="interface_selector_clear"></div>
		</dd>
	</dl>
	
<?php
}
?>
	
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
<link rel="stylesheet" type="text/css" href="/phphand2/shop/PHPHand/taglib/ui/selector/interface_selector_app/taglib/app/pagebar/style/pagebar.css">
<? echo '<p class="interface_selector_pagebar">'.$pagestr.'</p>';?>
<?
}
?>
</div>