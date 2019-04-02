<?php
if(!file_exists($this->env->get('data_dir').'/Cache/plugin_topic_flag.flag')){
	$this->db->query("CREATE TABLE IF NOT EXISTS `plugin_topic`(`id` int(10) NOT NULL auto_increment,`title` varchar(200) NOT NULL,`content` text NOT NULL,`createdate` int(10) NOT NULL,`updatetime` int(10) NOT NULL,`table` varchar(50) NOT NULL,`tid` int(10) NOT NULL default 0,`ip` varchar(20) NOT NULL,`uid` int(10) NOT NULL default 0,`username` varchar(20) NULL,`picture` varchar(20) NULL,recent_uid int(10),recent_username varchar(50) NULL,postnum int(10) NOT NULL DEFAULT 0,PRIMARY KEY  (`id`))");
	$___phphand_handle=fopen($this->env->get('data_dir').'/Cache/plugin_topic_flag.flag','w');
	fclose($___phphand_handle);
}
$___plugin_topic_id=$this->query->get('topic_id');
if(!$___plugin_topic_id) exit('topic id required');
$___plugin_topic=$this->db->getOne("SELECT * FROM plugin_topic WHERE id=$___plugin_topic_id");
if(!$___plugin_topic) exit('topic not exists');
?>
<div id="plugin_topic">
	<h5>{$___plugin_topic.title}</h5>
	<plugin:comment table="plugin_topic" key="$___plugin_topic_id" istopic="true" />
</div>