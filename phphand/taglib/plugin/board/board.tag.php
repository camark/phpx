<?php
if(!file_exists($this->env->get('data_dir').'/Cache/plugin_board_flag.flag')){
	$this->db->query("CREATE TABLE IF NOT EXISTS `plugin_board`(`id` int(10) NOT NULL auto_increment,`name` varchar(200) NOT NULL,`text` text NOT NULL,`createdate` int(10) NOT NULL,`pid` int(10) NOT NULL default 0,`topicnum` int(10) NOT NULL default 0,PRIMARY KEY  (`id`))");
	$___phphand_handle=fopen($this->env->get('data_dir').'/Cache/plugin_board_flag.flag','w');
	fclose($___phphand_handle);
}
$___plugin_board_id=$this->query->get('board_id');
if(!$___plugin_board_id) exit('board id required');
$___plugin_board=$this->db->getOne("SELECT * FROM plugin_board WHERE id=$___plugin_board_id");
if(!$___plugin_board) exit('board not exists');
$___child_board_sql="SELECT * FROM plugin_board WHERE pid=$___plugin_board_id";
$___plugin_board_url_base='';
$___plugin_querys=$this->query->get();
foreach($
?>
<div>
<PHPHand:list sql="$___child_board_sql" handle="$pcboard">
	<div>
		<b>{$pcboard.name}</b>
		<td>{$pcboard.
	</dov>
</PHPHand:list>
</div>