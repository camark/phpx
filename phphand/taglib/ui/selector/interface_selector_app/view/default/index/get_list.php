<div class="interface_selector_box_content_list">
	<ul>
		<phphand:mainlist sql="$sql" handle="$rs" page="$page" pagesize="10">
		<li value="{$rs[$value_column]}" title="{$rs[$show_column]}">{if mb_strlen($rs[$show_column],'utf-8')>12}<?php echo mb_substr($rs[$show_column],0,10,'utf-8').'..';?>{else}{$rs[$show_column]}{/if}</li>
		</phphand:mainlist>
		<div class="interface_selector_clear"></div>
	</ul>
	<app:pagebar />
</div>