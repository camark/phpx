<?php $checkcode=isset($checkcode)?++$checkcode:0;?>
<a class="a" id="PHPHAND_CHECKCODE{$checkcode}" href="javascript:ReloadCodeUiPicture{$checkcode}();"><img src="__PHP__?class={ui:checkcode}" height="34" /></a>
<script language="javascript"><!--
function ReloadCodeUiPicture{$checkcode}(){
	var url = '__PHP__?class={ui:checkcode}&rnd='+new Date()+Math.random();
	document.getElementById('PHPHAND_CHECKCODE{$checkcode}').childNodes[0].src=url;
}
//-->
</script>