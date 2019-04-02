<?php if(!isset($page_title)) $page_title='新页面';?>
{static:prev}
<div page_title="{$page_title}"{if isset($create_page_instance)} id="{$create_page_instance}"{/if}>
	<base:csshock /><?php $jqueryBasic=true;$jqueryUI=true;$jqueryAjaxPost=true;?>{layout}
	<script type="text/javascript">
	if(typeof pageSetUp=='function') pageSetUp();
	</script>
	<!-- <div class="page-time text-center">页面执行时间:<phphand:clock /></div> -->
</div>