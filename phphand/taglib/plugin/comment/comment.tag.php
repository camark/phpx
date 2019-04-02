<define:table required="true" />
<define:key required="false" default="0" />
<define:pagesize type="int" required="false" default="15" />
<define:userfacefolder required="false" />
<define:style required="false" default="default" />
<define:formtitle required="false" default="发表评论" />
<define:keyrow required="false" />
<define:countrow required="false" />
<define:istopic type="bool" required="false" default="false" />
<?php
/**
 * phphand comment plugin tag
 * first check the database flag
 * if the flag is false
 *   check database if there is a table named plugin_comment
 *   if not build this table
 *   if build fail exit tag
 *   else create the flag
 * 
 * 
 */
if(!file_exists($this->env->get('data_dir').'/Cache/plugin_comment_flag.flag')){
	$this->db->query("CREATE TABLE IF NOT EXISTS `plugin_comment`(`id` int(10) NOT NULL auto_increment,`content` text NOT NULL,`uid` int(10) NOT NULL default 0,`createdate` int(10) NOT NULL,`table` varchar(20) NULL,`tid` int(10) NOT NULL default 0,`ip` varchar(20) NOT NULL,`username` varchar(20) NULL,`picture` varchar(20) NULL,`order` int(10) NOT NULL default 1,PRIMARY KEY  (`id`))");
	$___phphand_handle=fopen($this->env->get('data_dir').'/Cache/plugin_comment_flag.flag','w');
	fclose($___phphand_handle);
}
$___phphand_comment_key=$param.key;
$___phphand_sql="SELECT * FROM plugin_comment WHERE `table`='{$param.table}' AND tid=$___phphand_comment_key ORDER BY id ASC";
?>
<div id="plugin-comment-list">
<PHPHand:mainlist sql="$___phphand_sql" handle="$___phphand_comment" pagesize="{$param.pagesize}">
	<div class="comment-item">
		<span class="comment-user">
			{if $___phphand_comment.uid>0}<a href="?class=member&method=profile&id={$___phphand_comment.uid}">{/if}
			<phphand:userface picture="$___phphand_comment.picture" />
			<span class="comment-username"><? echo preg_replace('/\.[^\.]+?$/is','.*',$___phphand_comment.ip);?></span>
			{if $___phphand_comment.uid>0}</a>{/if}
		</span>
		<div class="comment-detail">
			<p class="comment-info"><label class="f">#{$___phphand_comment.order}</label><? echo date('Y-m-d h:i',$___phphand_comment.createdate);?> | <a href="#__phphand_comment_post_form">回复</a></p>
			<div class="comment-content">
				<? echo str_replace("\n","<br/>",$___phphand_comment.content);?>
			</div>
		</div>
	</div>
	</PHPHand:mainlist>
	<PHPHand:pagebar size="5" />
</div>
<link rel="stylesheet" href="__TAG__/style/{$param.style}.css" type="text/css" />
<form id="__phphand_comment_post_form" name="__phphand_comment_post_form" method="post" action="__TAG__/post.php">
	<fieldset>
		<legend>{$param.formtitle}</legend>
		<input type="hidden" name="app_dir" value="<? echo $this->env->get('app_dir')?>" />
		<input type="hidden" name="table" value="{$param.table}" />
		<input type="hidden" name="tid" value="{$param.key}" />
		{if $param.istopic}
		<input type="hidden" name="istopic" value="true" />
		{/if}
		{if !is_null($param.countrow) && !is_null($param.keyrow)}
		<input type="hidden" name="countrow" value="{$param.countrow}" />
		<input type="hidden" name="keyrow" value="{$param.keyrow}" />
		{/if}
		<p>
		  <label for="dummy2">内容</label><br>
		  <Html:ckeditor name="content" toolbar="basic"></Html:ckeditor>
		</p>
		<p>
		  <input type="submit" name="submit" value="提交">
		</p>
	</fieldset>
</form>
