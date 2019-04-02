<? function msg($msg,$url=''){?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>消息提示</title>
	</head>
	<style type="text/css">
	<!--
	*{margin:0;padding:0;border:none;font-size:12px;}
	.shadow{
	background:#eee;
	margin:180px auto;
	padding:5px;
	width:500px;
	}
	.msg{
	padding:0;
	background:#f2f2f2;
	border:1px solid #ccc;
	padding:7px;
	}
	.msg h2{font-family:Arial, Helvetica, sans-serif;text-align:center;padding:7px;border-bottom:1px solid #ccc;color:#444;}
	.msg .i{padding:7px 7px 0;color:#000;text-align:center;}
	.msg .tl{margin:7px;text-align:center;color:#666;}
	.msg .tl a{color:#666;}
	.msg .tl a:hover{color:#039;}
	-->
	</style>
	<body>
	<div class="shadow">
	<div class="msg">
		<h2>消息提示</h2>
		<div class="i"><?=$msg?></div>
		<? if($url<>""){?>
		<? }else{?>
			<? if($_SERVER['HTTP_REFERER']==$_SERVER['PHP_SELF']){?>
				<? $url='./';?>
			<? }else{?>
				<? $url=$_SERVER['HTTP_REFERER'];?>
			<? }?>
		<? }?>
		<p class="tl">剩余<span id="tl"></span>秒即将自动为您定位到相应页面</p>
		<p class="tl"><a href="<?=$url?>">如果您的浏览器没有自动转向，请点击这个链接</a></p>
		<script language="javascript">
		var timeLast=5;
		function dup(){
			if(timeLast>0){
				timeLast--;
				document.getElementById('tl').innerHTML=timeLast;
				setTimeout("dup()",1000);
			}else{
				location.href='<?=$url?>';
			}
		}
		dup();
		</script>
	</div></div>
	</body>
	</html>
<? }?>