<?php
include 'msg.php';
include $_POST['app_dir'].'/config.php';

function getOrder($table,$tid){
	$query=mysql_query("SELECT `order` FROM plugin_comment WHERE `table`='$table' AND tid=$tid ORDER BY `order` DESC LIMIT 1");
	$rs=mysql_fetch_array($query);
	if(!$rs) return 1;
	else return $rs['order']+1;
}

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
$content=$_POST['content'];
$order=getOrder($table,$tid);

@mysql_query("INSERT INTO plugin_comment(content,uid,username,picture,`table`,tid,createdate,ip,`order`) VALUES('$content',$uid,'$username','$picture','$table',$tid,$createdate,'$ip',$order)");
if($table=='plugin_topic'){
	/**
	 * 为topic特别更新语句
	 */
	echo "UPDATE plugin_topic SET recent_uid=$uid,recent_username='$username',updatetime=$createdate,postnum=postnum+1,recent_ip='$ip' WHERE id=$tid";
	@mysql_query("UPDATE plugin_topic SET recent_uid=$uid,recent_username='$username',updatetime=$createdate,postnum=postnum+1,recent_ip='$ip' WHERE id=$tid");
}elseif(isset($_POST['countrow']) && isset($_POST['keyrow'])){
	//否则可能需要更新回复的数量
	@mysql_query("UPDATE $table SET {$_POST['countrow']}={$_POST['countrow']}+1 WHERE {$_POST['keyrow']}=$tid");
}
msg('操作成功!');
?>