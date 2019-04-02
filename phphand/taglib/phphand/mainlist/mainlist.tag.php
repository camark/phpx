<define:pagesize required="false" default="15" />
<define:sql type="*" required="true" />
<define:handle type="var" required="false" default="$rs" />
<define:size_handle type="var" required="false" default="$record_number" />
<define:lang_switch type="bool" required="false" default="false" />
<?php
if($param.pagesize<=0){
	exit('cant define a below 0');
}
if(!isset($___phphand_pagecount)){
	$___phphand_sql=preg_replace("/ORDER BY .+?$/is","",preg_replace("/^SELECT\s(.+?)FROM/is","SELECT COUNT(*) n FROM",$param.sql));
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
		$___html_page_data=array('sql'=>$___phphand_sql,'pagesize'=>$param.pagesize);
		$this->share->set('html_page_data_'.strtolower($this->env->get('class').'-'.$this->env->get('method')).'-'.$___flag,$___html_page_data);
	}
	$___phphand_query=$this->db->query($___phphand_sql,$param.lang_switch);
	$___phphand_mainresult=$this->db->fetchArray($___phphand_query);
	if(isset($param.size_handle) && $param.size_handle){
		$param.size_handle=$___phphand_mainresult['n'];
	}
	$___phphand_pagecount=ceil($___phphand_mainresult['n']/$param.pagesize);
	$this->sign('page_count',$___phphand_pagecount);
	$___phphand_page=intval($this->query->get('page'));
	if(!$___phphand_page || $___phphand_page<1){
		$___phphand_page=1;
	}elseif($___phphand_page>$___phphand_pagecount){
		$___phphand_page=$___phphand_pagecount;
	}
}
$___phphand_list_start=($___phphand_page-1)*$param.pagesize;
$___phphand_list_size = $param.pagesize;
$___phphand_list_sql  = $param.sql . " LIMIT $___phphand_list_start,$___phphand_list_size";
$___phphand_mainquery = $this->db->query($___phphand_list_sql,$param.lang_switch);
$mc=0;
while($param.handle = $this->db->fetchArray($___phphand_mainquery)){
$mc++;
?>
__HTML__
<?php
}
?>