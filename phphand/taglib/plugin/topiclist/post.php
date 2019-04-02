<?php
include 'msg.php';
include $_POST['app_dir'].'/config.php';

$db_name=$config['db_name'];
$db_username=$config['db_username'];
$db_password=$config['db_password'];
$db_host=$config['db_host'];
$conn=@mysql_connect($db_host,$db_username,$db_password);
@mysql_select_db($db_name,$conn);
@mysql_query("SET NAMES 'UTF8'"); 
@mysql_query("SET CHARACTER SET UTF8"); 
@mysql_query("SET CHARACTER_SET_RESULTS=UTF8'");

if(!isset($_SESSION) || $_SESSION['grouptype']=='guest'){
	$uid=0;
	$username='guest';
}else{
	$uid=$_SESSION['id'];
	$username=$_SESSION['name'];
	$userq=@mysql_query("SELECT * FROM phphand_member WHERE id=$uid");
	$user=@mysql_fetch_array($userq);
	$picture=$user['picture'];
}
$ip=getenv('REMOTE_ADDR');
$createdate=time();
$table=$_POST['table'];
$tid=$_POST['tid'];
$title=$_POST['title'];
$content=$_POST['content'];

@mysql_query("INSERT INTO plugin_topic(title,content,uid,username,picture,`table`,tid,createdate,ip,recent_uid,recent_username,recent_ip,updatetime,postnum) VALUES('$title','$content',$uid,'$username','$picture','$table',$tid,$createdate,'$ip',$uid,'$username','$ip',$createdate,1)");
$topicId=@mysql_insert_id();
@mysql_query("INSERT INTO plugin_comment(content,uid,username,picture,`table`,tid,createdate,ip,`order`) VALUES('$content',$uid,'$username','$picture','plugin_topic',$topicId,$createdate,'$ip',1)");

if($_POST['table']=='plugin_topic_category'){
	/**
	 * 为topic特别更新语句
	 */
	//@mysql_query("UPDATE plugin_topic SET recent_uid=$uid,recent_username='$username',updatetime=$createdate,postnum=postnum+1 WHERE id=$tid");
}elseif(isset($_POST['countrow']) && isset($_POST['keyrow'])){
	//否则可能需要更新回复的数量
	//@mysql_query("UPDATE $table SET {$_POST['countrow']}={$_POST['countrow']}+1 WHERE {$_POST['keyrow']}=$tid");
}
msg('操作成功!');
?>