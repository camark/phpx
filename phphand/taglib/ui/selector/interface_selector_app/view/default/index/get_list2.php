<div class="interface_selector_box_content_list">
	<phphand:mainlist sql="$sql" handle="$rs" pagesize="6">
	<dl>
		<dt>
			<span value="{$rs[$value_column]}" title="{$rs[$show_column]}">{$rs[$show_column]}</span>
		</dt>
		<dd>
			<?php $sql=str_replace('$.'.$column,$rs[$column],$sql2);?>
			<phphand:list sql="$sql" handle="$rs2">
			<span value="{$rs2[$value_column]}" title="{$rs2[$show_column]}">{$rs2[$show_column]}</span>
			</phphand:list>
			<div class="interface_selector_clear"></div>
		</dd>
	</dl>
	</phphand:mainlist>
	<app:pagebar />
</div>