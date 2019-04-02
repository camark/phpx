<define:table default="plugin_topic_category" />
<define:key default="0" />
<define:method default="down" />
<define:pagesize required="false" default="15" />
<define:url required="false" default="?class=index&method=topic" />
<define:style required="false" default="default" />
<?php
if(!file_exists($this->env->get('data_dir').'/Cache/plugin_topic_flag.flag')){
	$this->db->query("CREATE TABLE IF NOT EXISTS `plugin_topic`(`id` int(10) NOT NULL auto_increment,`title` varchar(200) NOT NULL,`content` text NOT NULL,`createdate` int(10) NOT NULL,`updatetime` int(10) NOT NULL,`table` varchar(50) NOT NULL,`tid` int(10) NOT NULL default 0,`ip` varchar(20) NOT NULL,`uid` int(10) NOT NULL default 0,`username` varchar(20) NULL,`picture` varchar(20) NULL,recent_uid int(10),recent_username varchar(50) NULL,recent_ip varchar(20) NOT NULL,postnum int(10) NOT NULL DEFAULT 0,viewnum int(10) NOT NULL DEFAULT 0,PRIMARY KEY  (`id`))");
	$___phphand_handle=fopen($this->env->get('data_dir').'/Cache/plugin_topic_flag.flag','w');
	fclose($___phphand_handle);
}
if(!file_exists($this->env->get('data_dir').'/Cache/plugin_comment_flag.flag')){
	$this->db->query("CREATE TABLE IF NOT EXISTS `plugin_comment`(`id` int(10) NOT NULL auto_increment,`content` text NOT NULL,`uid` int(10) NOT NULL default 0,`createdate` int(10) NOT NULL,`table` varchar(20) NULL,`tid` int(10) NOT NULL default 0,`ip` varchar(20) NOT NULL,`username` varchar(20) NULL,`picture` varchar(20) NULL,`order` int(10) NOT NULL default 1,PRIMARY KEY  (`id`))");
	$___phphand_handle=fopen($this->env->get('data_dir').'/Cache/plugin_comment_flag.flag','w');
	fclose($___phphand_handle);
}
$___plugin_topic_list_sql="SELECT * FROM plugin_topic WHERE `table`='{$param.table}' ORDER BY updatetime DESC";
?>
<link rel="stylesheet" href="__TAG__/style/{$param.style}.css" type="text/css" />
<div class="plugin_topic_list_div">
	<table class="plugin_topic_list" width="100%" border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<td width="40">类型</td>
			<td width="60%">标题</td>
			<td>作者/时间</td>
			<td class="center">回复/查看</td>
			<td class="right">最后发表</td>
		</tr>
	</thead>
	
	<tbody>
	<PHPHand:mainlist sql="$___plugin_topic_list_sql" handle="$___plugin_topic" pagesize="{$param.pagesize}">
		<tr>
			<td><img src="__TAG__/style/folder_new.gif" /></td>
			<td><a href="{$param.url}&topic_id={$___plugin_topic.id}">{$___plugin_topic.title}</a></td>
			<td class="c">
			{if $___plugin_topic.uid>1}<a href="?class=member&method=profile&id={$___plugin_topic.uid}">{$___plugin_topic.username}</a>{else}{$___plugin_topic.ip}{/if}
			<br />
			<span><? echo date('Y-m-d',$___plugin_topic.createdate);?></span>
			</td>
			<td class="center">{$___plugin_topic.postnum}/{$___plugin_topic.viewnum}</td>
			<td class="right u">
			{if $___plugin_topic.recent_uid>1}<a href="?class=member&method=profile&id={$___plugin_topic.recent_uid}">{$___plugin_topic.recent_username}</a>{else}{$___plugin_topic.recent_ip}{/if}
			<br />
			<span><? echo date('Y-m-d',$___plugin_topic.updatetime);?></span>
			</td>
		</tr>
	</PHPHand:mainlist>
	</tbody>
	</table>
	{if $param.method=='down'}
		<form name="___plugin_topic_post_form" method="post" action="__TAG__/post.php">
		<fieldset>
			<legend>发表帖子</legend>
			<input type="hidden" name="app_dir" value="<? echo $this->env->get('app_dir')?>" />
			<input type="hidden" name="table" value="{$param.table}" />
			<input type="hidden" name="tid" value="{$param.key}" />
			<p>
				<label>标题</label><br />
				<input type="text" class="title" name="title" maxlength="50" />
			</p>
			<p>
			  <label for="dummy2">内容</label><br>
			  <Html:ckeditor name="content" toolbar="basic"></Html:ckeditor>
			</p>
			<p>
			  <input type="submit" name="submit" value="提交">
			</p>
		</fieldset>
		</form>
	{/if}
</div>