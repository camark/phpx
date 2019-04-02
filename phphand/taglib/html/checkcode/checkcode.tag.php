<?php
/**
 * ---------------------------------------------------
 * PHPHand Checkcode标签
 *
 * 功能：验证码
 *
 * @文件：Checkcode.Tag.php
 * @包：PHPHand.taglib.PH
 *
 * @日期：2009/8/5
 * @作者：张文杰
 * @如有任何建议，请发送至作者邮箱：prettysite@qq.com
 * ---------------------------------------------------
 *
 * 格式：
 * < ph:checkcode
 * 		/ >
 * ---------------------------------------------------
 */
?>
<?php $checkcode=isset($checkcode)?++$checkcode:0;?>
<style type="text/css"><!--
a.a img{vertical-align:top;}
--></style>
<a class="a" id="PHPHAND_CHECKCODE{$checkcode}" href="javascript:ReloadCodePicture{$checkcode}();"><img src="__TAG__/Checkcode/CodePicture.php" /></a>
<script language="javascript"><!--
function ReloadCodePicture{$checkcode}(){
	document.getElementById('PHPHAND_CHECKCODE{$checkcode}').childNodes[0].src='__TAG__/Checkcode/CodePicture.php?rnd='+new Date()+Math.random();
}
//-->
</script>